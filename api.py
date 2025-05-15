from flask import Flask, request, jsonify
import requests
import pymysql
import json
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

from dotenv import load_dotenv
import os
load_dotenv()
FIREWORKS_API_KEY = os.environ.get("FIREWORKS_API_KEY")
FIREWORKS_URL = "https://api.fireworks.ai/inference/v1/chat/completions"

# --- DB CONFIG (adjust as needed) ---
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "edu_users",
}


def get_student_data(parent_email):
    conn = pymysql.connect(**DB_CONFIG)
    cur = conn.cursor(pymysql.cursors.DictCursor)
    cur.execute(
        "SELECT student_id, classes FROM parents_students WHERE email=%s",
        (parent_email,),
    )
    student = cur.fetchone()
    if not student:
        conn.close()
        return None, None
    cur.execute(
        "SELECT participation, understanding, behavior, emotional, notes FROM report_history WHERE student_id=%s",
        (student["student_id"],),
    )
    reports = cur.fetchall()
    conn.close()
    return student, reports


@app.route("/ai_recommendation", methods=["POST"])
def ai_recommendation():
    parent_email = request.json.get("parent_email")
    if not parent_email:
        return jsonify({"error": "Missing parent_email"}), 400
    student, reports = get_student_data(parent_email)
    if not student:
        return jsonify({"error": "Student not found"}), 404

    # Aggregate
    sums = {"participation": 0, "understanding": 0, "behavior": 0, "emotional": 0}
    notes = []
    for r in reports:
        for k in sums:
            sums[k] += int(r.get(k, 0))
        if r.get("notes"):
            notes.append(r["notes"])

    grade = student["classes"]
    prompt = (
        f"Hey, so I am a student in grade {grade} and I have a sum of reports of "
        f"{sums['participation']} in participation, {sums['understanding']} in understanding, "
        f"{sums['behavior']} in behavior, {sums['emotional']} in emotional. "
        f"Could you please tell me, what does it say about me? And how can I improve myself? "
        f"My teacher also left me these notes: {', '.join(notes)}. "
        "Additionally, based on this data, please suggest: "
        "- A possible future career path that fits my strengths (future_career), "
        "- What my main interests might be (child_interest), "
        "- My likely learning style (learning_style), "
        "- A hobby I might enjoy (recommended_hobby), "
        "- A tip for my parent to help me grow (parent_tips). "
        "Reply ONLY in raw JSON with these keys: ai_notes, youtube_link_to_improve, youtube_link_for_improving, future_career, child_interest, learning_style, recommended_hobby, parent_tips. "
        "Do NOT explain, do NOT use markdown, do NOT add headings. Just reply with the JSON object."
    )

    # Fireworks API call
    payload = {
        "model": "accounts/fireworks/models/llama4-maverick-instruct-basic",
        "max_tokens": 2048,
        "temperature": 0.6,
        "messages": [{"role": "user", "content": [{"type": "text", "text": prompt}]}],
    }
    headers = {
        "Accept": "application/json",
        "Content-Type": "application/json",
        "Authorization": f"Bearer {FIREWORKS_API_KEY}",
    }
    resp = requests.post(FIREWORKS_URL, headers=headers, json=payload)
    if resp.status_code == 200:
        try:
            content = resp.json()["choices"][0]["message"]["content"]
            ai_json = None
            try:
                ai_json = json.loads(content)
            except Exception:
                ai_json = {"ai_notes": content}

            # --- YouTube validation ---
            def extract_youtube_id(url):
                import re
                match = re.search(r'(?:v=|youtu\.be/)([A-Za-z0-9_-]{11})', url or '')
                return match.group(1) if match else None

            def is_valid_youtube_video(url):
                yt_id = extract_youtube_id(url)
                if not yt_id:
                    return False
                oembed_url = f"https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={yt_id}&format=json"
                try:
                    r = requests.get(oembed_url, timeout=2)
                    return r.status_code == 200
                except Exception:
                    return False

            for key in ["youtube_link_to_improve", "youtube_link_for_improving"]:
                url = ai_json.get(key)
                if url and not is_valid_youtube_video(url):
                    ai_json[key] = None

            return jsonify(ai_json)

        except Exception as e:
            return jsonify({"error": f"AI response error: {str(e)}"}), 500
    else:
        return jsonify({"error": "Failed to get AI recommendation"}), 500


if __name__ == "__main__":
    app.run(debug=True)
