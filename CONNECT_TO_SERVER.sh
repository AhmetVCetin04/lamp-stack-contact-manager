SSH_KEY="./SERVER_KEY"
SERVER_USER="root"
SERVER_ADDR="165.232.128.10"

# simple ssh into the server
echo "SSHing to server (looking for key filename '$SSH_KEY'...)"
ssh \
  -i "$SSH_KEY" \
  "$SERVER_USER@$SERVER_ADDR" 
