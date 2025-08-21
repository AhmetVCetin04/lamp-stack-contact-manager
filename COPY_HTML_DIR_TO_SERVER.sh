SSH_KEY="./SERVER_KEY"
HTML_DIR="./html/"
ABS_SERVER_HTML_PATH="root@165.232.128.10:/var/www/"

# scp, give ssh key
scp \
  -r \
  -i "$SSH_KEY" \
  "$HTML_DIR" \
  "$ABS_SERVER_HTML_PATH"
