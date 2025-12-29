#!/bin/bash

git add .

echo " Enter commit message:"
read message
if [ -z "$message" ]; 
then
    message="Update from script: $(date)"
fi

git commit -m "$message"

git pull --rebase origin main

if [ $? -ne 0 ];
 then
    echo " Pull failed. Resolve conflicts manually."
    exit 1
fi

git push origin main

if [ $? -eq 0 ];
 then
    echo " Success! Jenkins will start the build now."
else
    echo "Push failed."
fi