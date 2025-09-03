SSH_KEY=".ssh/SERVER_KEY"
PROJECT_DIR="./"
SERVER_USER="root"
SERVER_ADDR="165.232.128.10"
SERVER_WEB_PATH="/var/www/html"
SERVER_API_PATH="/var/www/html/api"

copy_project() {
    echo "Deploying Contact Manager to server..."
    echo "Looking for SSH key: '$SSH_KEY'"
    
    # Copy entire project (frontend + API)
    echo "Copying project files..."
    scp -r -i "$SSH_KEY" ./html/* "$SERVER_USER@$SERVER_ADDR:$SERVER_WEB_PATH/"
    
    echo "Deployment completed successfully!"
    echo "Your contact manager is available at: http://165.232.128.10/"
    echo "API endpoints are at: http://165.232.128.10/api/"
}

# Run the deployment
copy_project
