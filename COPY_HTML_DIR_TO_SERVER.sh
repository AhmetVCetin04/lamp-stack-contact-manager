SSH_KEY=".ssh/SERVER_KEY"
HTML_DIR="./html/"
ABS_SERVER_HTML_PATH="root@165.232.128.10:/var/www/"

# scp, give ssh key
echo "Copying HTML dir to server (looking for key filename '$SSH_KEY'...)"
scp \
  -r \
  -i "$SSH_KEY" \
  "$HTML_DIR" \
  "$ABS_SERVER_HTML_PATH"
