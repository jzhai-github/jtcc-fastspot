if expr $(git status --porcelain | wc -l) \> 0
then
  git config --global user.email "actions@github.com"
  git config --global user.name "GitHub Actions"
  git add .
  git commit -m "GitHub Actions Build"
  git push
else 
  echo "Working tree clean. Nothing to commit."
fi