SSH_KEY="../.ssh/SERVER_KEY"
HTML_DIR="../html/"
SERVER_USER="root"
SERVER_ADDR="165.232.128.10"
SERVER_HTML_PATH="/var/www/"

# scp, give ssh key
echo "Copying HTML dir to server (looking for key filename '$SSH_KEY'...)"
scp \
  -r \
  -i "$SSH_KEY" \
  "$HTML_DIR" \
  "$SERVER_USER@$SERVER_ADDR:$SERVER_HTML_PATH"
