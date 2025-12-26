
git add index.php

if git diff --cached --quiet; then
    echo "No new changes to push."
    exit 0
fi
echo "Enter commit message:"
read message
git commit -m "$message"
git push origin main

echo "Changes have been successfully pushed to GitHub"