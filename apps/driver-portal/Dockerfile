# Build stage
FROM node:20-alpine AS build
WORKDIR /app
COPY . .
RUN npm install
RUN npm run build

# Run stage
FROM node:20-alpine
WORKDIR /app
COPY --from=build /app/.output ./.output

EXPOSE 3000
ENV PORT=3000
CMD ["node", ".output/server/index.mjs"]
