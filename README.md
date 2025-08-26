# Drinkkiarkisto

*A final-year school project for the second academic year.*

---

## 📖 About the project

**Drinkkiarkisto** is a web-based cocktail archive created as part of a vocational software development programme. The application allows users to search, view, and suggest drink recipes, while administrators can manage contributions and moderate the archive.

The project was designed to demonstrate practical skills in full‑stack web development, covering front‑end interactivity, server‑side logic, and database design.

---

## ✨ Features

* 🔍 **Search & filter** drinks by name, ingredient, or category
* 📜 **Recipe pages** with ingredients, instructions, and like/dislike ratings
* ➕ **Suggest a new drink** with categories, allergens, ingredients, and instructions
* 👥 **User accounts** with login & registration
* 🛠️ **Admin tools**

  * Manage drink suggestions
  * Edit or remove drinks
  * Manage users
* 🔒 **Authentication & roles** with elevated rights for admins

---

## 🛠️ Tech stack

* **Backend:** PHP (procedural, with includes for modularity)
* **Database:** MySQL
* **Frontend:** HTML, CSS, JavaScript
* **Other:** Session-based authentication, basic access control, SQL-driven content

---

## 📂 Project structure (simplified)

```
drinkkiarkisto/
├── index.php              # Homepage
├── search.php             # Search interface
├── recipe.php             # Recipe detail page
├── create.php             # Suggest a new drink
├── manage-users.php       # Admin – user management
├── manage-drinks.php      # Admin – manage drinks
├── manage-suggestions.php # Admin – approve suggestions
├── includes/              # DB connection, auth, logic scripts
├── assets/js/             # Frontend JS (search.js)
├── assets/css/            # Styling
└── sql/                   # Database schema
```

---

## 🎓 Project context

This project was completed as a **final assignment (loppuprojekti)** for the second academic year. It served as a practical demonstration of database-driven web development, user access control, and admin panel functionality.

---

## ⚠️ Note

This repository reflects the project at the time of submission. It may contain experimental code, student‑level practices, or security limitations that were acceptable in the school context but should not be considered production‑ready.
