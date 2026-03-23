#!/bin/bash

# Golampi IDE Quick Start Script
# Este script inicia el backend y frontend simultáneamente

echo "🚀 Starting Golampi IDE..."
echo ""

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -d "Backend" ] || [ ! -d "Frontend" ]; then
    echo -e "${RED}❌ Error: Must run from project root${NC}"
    echo "Current directory: $(pwd)"
    exit 1
fi

# Start Backend (use api.php as router so /api/* routes work)
echo -e "${BLUE}Starting Backend on port 8000...${NC}"
cd Backend
# Use api.php as router to ensure all /api requests are handled by the router
php -S localhost:8000 api.php -t . > /tmp/golampi-backend.log 2>&1 &
BACKEND_PID=$!
echo -e "${GREEN}✓ Backend started (PID: $BACKEND_PID)${NC}"
cd ..

# Wait for backend
sleep 2

# Check backend
if ! kill -0 $BACKEND_PID 2>/dev/null; then
    echo -e "${RED}❌ Backend failed to start${NC}"
    cat /tmp/golampi-backend.log
    exit 1
fi

# Start Frontend
echo -e "${BLUE}Starting Frontend on port 5173...${NC}"
cd Frontend

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo -e "${BLUE}Installing Frontend dependencies...${NC}"
    npm install > /tmp/golampi-npm.log 2>&1
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ npm install failed${NC}"
        cat /tmp/golampi-npm.log
        kill $BACKEND_PID
        exit 1
    fi
fi

npm run dev > /tmp/golampi-frontend.log 2>&1 &
FRONTEND_PID=$!
echo -e "${GREEN}✓ Frontend started (PID: $FRONTEND_PID)${NC}"
cd ..

# Wait for frontend
sleep 3

# Check frontend
if ! kill -0 $FRONTEND_PID 2>/dev/null; then
    echo -e "${RED}❌ Frontend failed to start${NC}"
    cat /tmp/golampi-frontend.log
    kill $BACKEND_PID
    exit 1
fi

# Success message
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}    Golampi IDE is now running!    ${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}Frontend:${NC} http://localhost:5173"
echo -e "${BLUE}Backend API:${NC} http://localhost:8000/api"
echo ""
echo -e "${RED}Press Ctrl+C to stop${NC}"
echo ""

# Keep script running and handle Ctrl+C
trap "kill $BACKEND_PID $FRONTEND_PID 2>/dev/null; echo ''; echo 'Stopped.'; exit" SIGINT
wait