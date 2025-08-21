REL_SERVER_KEY_PATH="SERVER_PASSWORD.txt"
ABS_LOCAL_PATH="/Users/rafa/Developer/POOSD/html/"
ABS_SERVER_PATH="root@165.232.128.10:/var/www/"

# get server password from file, we should not have this in git repo for security
echo "Getting server pass... (Looking for filename $REL_SERVER_KEY_PATH)"
server_pass="$(cat $REL_SERVER_KEY_PATH)"

if [[ -z "$server_pass" ]]
  then echo "Could not find pass, aborting..."
fi

echo "Found server pass, transferring files..."

# scp entire html dir to /var/www/ in server
# FIXME: requires installing sshpass! we should probably use ssh keys
sshpass -p \
  "$server_pass" \
  scp -r \
  "$ABS_LOCAL_PATH" \
  "$ABS_SERVER_PATH" \

# curl version, doesn't require sshpass dep but is slower
# curl \
#   --insecure \
#   --user "root:$server_pass" \
#   -T "$ABS_LOCAL_PATH" \
#   "ftp://$ABS_SERVER_PATH"

echo "Done!"
