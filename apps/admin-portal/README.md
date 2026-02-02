# 🚀 Y-Drive Admin Portal

This project is the **admin portal** for the Y-Drive platform, built using **Nuxt.js**, **Vue 3**, and **TypeScript**.
Follow this guide to set up the project locally and contribute effectively.

---

## 📦 Tech Stack

* **Nuxt.js 3**
* **Vue 3 Composition API**
* **TypeScript**
* **TailwindCSS** (optional)
* **Vite** (build tool)

---

## 🛠️ Local Development Setup

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/YDriveApp/y-drive-admin-portal.git
```

---

### 2️⃣ Navigate to the Project Directory & Install Dependencies

```bash
cd y-drive-admin-portal

# Install required dependencies
npm install
```

---

### 3️⃣ Environment Variables

Create a `.env` file in the project root (or copy from `.env.example`) and configure the following variables:

```env
# Example environment variables
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

> ⚠️ Make sure to keep sensitive information like API keys or tokens secure.

---

### 4️⃣ Start the Development Server

```bash
npm run dev
```

The app will be available at:

```
http://localhost:3000
```

---

## 📁 Project Structure

```bash
├── assets/         # Global styles, images, resources
├── components/     # Vue components
├── composables/    # Reusable logic (auto-imported by Nuxt)
├── layouts/        # Application layouts
├── pages/          # Route-based pages
├── plugins/        # Nuxt plugins
├── public/         # Static assets
├── server/         # API routes (if using Nuxt server)
└── nuxt.config.ts  # Nuxt configuration
```

---

## 🧪 Useful Scripts

```bash
npm run dev        # Start local development server
npm run build      # Build for production
npm run preview    # Preview production build
npm run lint       # Run linting checks
npm run test       # Run automated tests (if configured)
```

---

## 🌐 API Integration

* Ensure the `NUXT_PUBLIC_API_BASE` is correctly set in `.env`.
* Use Axios or Fetch for API calls.
* Example API call in a composable:

```ts
import { useFetch } from '#app'

export const useUsers = () => {
  const { data, error } = useFetch(`${process.env.API_BASE_URL}/users`)
  return { data, error }
}
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. **Fork the repository** and create a feature branch:

```bash
git checkout -b feature/my-new-feature
```

2. **Make your changes** with clear commit messages:

```bash
git commit -m "Add new feature XYZ"
```

3. **Push to your fork** and create a Pull Request.
4. Ensure **linting passes** and **tests run successfully** before submitting.

---

## 📬 Support

If you encounter any issues, please open a **GitHub issue** or contact the Y-Drive development team.

---
