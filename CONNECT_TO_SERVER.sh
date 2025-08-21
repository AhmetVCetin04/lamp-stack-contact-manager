SSH_KEY="./SERVER_KEY"
SERVER_USER="root"
SERVER_ADDR="165.232.128.10"

# simple ssh into the server
ssh \
  -i "$SSH_KEY" \
  "$SERVER_USER@$SERVER_ADDR" 
