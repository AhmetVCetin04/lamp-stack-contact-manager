SSH_KEY="../.ssh/SERVER_KEY"
SERVER_USER="root"
SERVER_ADDR="165.232.128.10"

# Copy entire dirs "-r" 
function deployDir () {
  # Get first arg (src dir)
  LOCAL_SRC="$1"
  SERVER_DEST="$2"

  echo "Copying $LOCAL_SRC to $SERVER_DEST (looking for key filename '$SSH_KEY'...)"

  scp \
    -r \
    -i "$SSH_KEY" \
    "$LOCAL_SRC" \
    "$SERVER_USER@$SERVER_ADDR:$SERVER_DEST"
}

# Copy regular files matching pattern 
function deployPat() {
  PATTERN="$1"
  SERVER_DEST="$2"

  echo "Copying files matching $PATTERN to $SERVER_DEST (looking for key filename '$SSH_KEY'...)"

  scp \
    -i "$SSH_KEY" \
    "$PATTERN" \
    "$SERVER_USER@$SERVER_ADDR:$SERVER_DEST"
}

# Match option to what we want to deploy
opt="$1"
echo "Received option '$opt'"
case "$opt" in
  # Option         # Source       # Dest
   api)  deployDir ../html/api/   /var/www/html/;;
  html)  deployPat ../html/*.html /var/www/html/;; # FIXME: This is broken?
   css)  deployDir ../html/css/   /var/www/html/;;
    js)  deployDir ../html/js/    /var/www/html/;;
     *)  deployDir ../html/       /var/www/;;
esac
