
cd ~/UnixProject


git add .
git reset config.php


echo "Enter commit message:"
read message


git commit -m "$message"
git push origin main

echo "Code uploaded successfully to GitHub (Config skipped)!"