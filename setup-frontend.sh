#!/bin/bash

echo "🚀 Setting up Vue 3 Dashboard..."
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    exit 1
fi

echo "✓ Node.js version: $(node --version)"
echo "✓ NPM version: $(npm --version)"
echo ""

# Install dependencies
echo "📦 Installing Node dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo ""
echo "✓ Dependencies installed successfully"
echo ""

# Build assets for development
echo "🔨 Building assets..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Failed to build assets"
    exit 1
fi

echo ""
echo "✅ Frontend setup complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Run 'npm run dev' for development with hot reload"
echo "   2. Visit http://localhost:8000/dashboard"
echo ""
echo "🎉 Happy coding!"
