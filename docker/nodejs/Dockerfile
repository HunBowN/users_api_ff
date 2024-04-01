FROM node:20

RUN apt-get update && apt-get install -y \
    vim \
    zip \
    unzip \
    curl

USER node

# WORKDIR /home/fatfox/frontend
WORKDIR /var/www/frontend
COPY ./ ./

EXPOSE 5173

CMD ["sh", "-c", "cd ./frontend && npm install"]
