import sqlite3

# Kết nối đến file SQLite
conn = sqlite3.connect("D:/D-Documents/outsource/webthueaccvalorant/web-thue-acc-valorant/database/app.sqlite")
cursor = conn.cursor()

# Câu SQL (bổ sung alias cho subquery)
sql = """
SELECT * 
FROM game_accounts
WHERE id < 72
ORDER BY created_at DESC, id DESC
LIMIT 10
"""

# Truyền tham số (ở đây là id < 72)
cursor.execute(sql)

# Lấy kết quả
rows = cursor.fetchall()

# In kết quả
for row in rows:
    print(row)

# Đóng kết nối
conn.close()