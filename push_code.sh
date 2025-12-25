#!/bin/bash

# الانتقال لمجلد المشروع
cd ~/UnixProject

# إضافة التعديلات
git add .

# طلب رسالة الـ Commit من المستخدم
echo "Enter commit message:"
read message

# عمل Commit و Push
git commit -m "$message"
git push origin main

echo "Code uploaded successfully to GitHub!"