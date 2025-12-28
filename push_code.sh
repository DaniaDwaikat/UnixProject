
#!/bin/bash

git pull --rebase origin main

if [ $? -ne 0 ]; then
    echo " Pull failed. Please resolve conflicts first."
    exit 1
fi

git add .

if git diff --cached --quiet; then
    echo " No new changes to push."
    exit 0
fi

echo " Enter commit message:"
read message

git commit -m "$message"
git push origin main

if [ $? -eq 0 ]; then
    echo " Changes were successfully pushed to GitHub."
else
    echo " Push failed. Changes were NOT pushed."
fi