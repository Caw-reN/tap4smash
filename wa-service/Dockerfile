FROM node:20-alpine

# Set direktori kerja di dalam kontainer
WORKDIR /app

# Salin definisi paket
COPY package*.json ./

# Install dependensi
RUN npm install

# Salin source code WA service
COPY . .

# Ekspos port tempat WA Service berjalan
EXPOSE 3001

# Perintah untuk menjalankan service
CMD ["node", "index.js"]
