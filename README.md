# ShopMe - Site E-commerce Laravel

Un site e-commerce moderne et complet développé avec Laravel, avec une interface responsive et un système de tabs pour mobile.

## Fonctionnalités

### ✅ Fonctionnalités principales

- **Page d'accueil intuitive** avec affichage des catégories, produits vedettes, promotions et nouveautés
- **Système de catégories hiérarchique** avec catégories parentes et sous-catégories
- **Gestion des produits** avec :
  - Statuts actifs/inactifs
  - Système de promotions (prix normal et prix promotionnel)
  - Gestion du stock
  - Images et descriptions
  - Produits vedettes
  
- **Authentification complète** avec rôles :
  - Admin
  - Client
  - Vendeur

- **Panier d'achat** avec gestion des quantités
- **Système de commandes** avec suivi des statuts
- **Profil utilisateur** avec système de tabs pour mobile
- **Interface responsive** adaptée mobile, tablette et desktop

### 🎨 Design

- Design moderne avec Tailwind CSS
- Interface intuitive et user-friendly
- Navigation responsive avec menu mobile
- Système de tabs pour le profil utilisateur sur mobile
- Animations et transitions fluides

## Installation

### Prérequis

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL ou SQLite
- Node.js et NPM (optionnel pour assets)

### Étapes d'installation

1. **Cloner le projet** (si applicable) ou naviguer dans le répertoire

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**

Éditer le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopme
DB_USERNAME=root
DB_PASSWORD=
```

5. **Créer la base de données et exécuter les migrations**
```bash
php artisan migrate
```

6. **Remplir la base de données avec des données d'exemple**
```bash
php artisan db:seed
```

7. **Démarrer le serveur de développement**
```bash
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

## Comptes par défaut

Après avoir exécuté le seeder, vous pouvez vous connecter avec :

### Admin
- Email: `admin@shopme.com`
- Mot de passe: `password`

### Client
- Email: `client@shopme.com`
- Mot de passe: `password`

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          # Contrôleurs d'authentification
│   │   ├── CartController.php
│   │   ├── CategoryController.php
│   │   ├── HomeController.php
│   │   ├── OrderController.php
│   │   └── ProductController.php
│   └── Middleware/
│       └── CheckRole.php  # Middleware pour les rôles
├── Models/
│   ├── CartItem.php
│   ├── Category.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   └── User.php
database/
├── migrations/            # Migrations de la base de données
└── seeders/
    └── EcommerceSeeder.php
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php  # Layout principal
    ├── auth/              # Pages d'authentification
    ├── cart/              # Pages du panier
    ├── category/          # Pages des catégories
    ├── home.blade.php     # Page d'accueil
    ├── orders/            # Pages des commandes
    ├── partials/          # Composants réutilisables
    ├── products/          # Pages des produits
    └── profile/           # Pages du profil
routes/
└── web.php               # Routes de l'application
```

## Fonctionnalités techniques

### Migrations

- `users` - Utilisateurs avec rôles
- `categories` - Catégories avec hiérarchie parent-enfant
- `products` - Produits avec promotions et stock
- `cart_items` - Articles du panier
- `orders` - Commandes
- `order_items` - Articles des commandes

### Modèles Eloquent

Tous les modèles incluent :
- Relations bien définies
- Scopes pour faciliter les requêtes
- Accesseurs pour calculs (prix, réductions, etc.)
- Casting approprié des types de données

### Routes

- Routes publiques : accueil, produits, catégories
- Routes authentifiées : panier, commandes, profil
- Routes d'authentification : login, register, logout

## Utilisation

### Navigation

1. **Accueil** : Page principale avec aperçu des produits
2. **Produits** : Liste avec filtres et tri
3. **Catégories** : Navigation par catégories
4. **Panier** : Gestion des articles
5. **Commandes** : Historique et détails des commandes
6. **Profil** : Informations utilisateur avec tabs sur mobile

### Ajout de produits au panier

1. Naviguer vers un produit
2. Sélectionner la quantité
3. Cliquer sur "Ajouter au panier"
4. Le produit sera visible dans le panier

### Passer une commande

1. Ajouter des produits au panier
2. Aller dans le panier
3. Cliquer sur "Passer la commande"
4. Remplir les informations de livraison
5. Sélectionner le mode de paiement
6. Confirmer la commande

## Développement futur

- Intégration de paiement (Stripe, PayPal)
- Gestion des avis clients
- Système de wishlist
- Notifications par email
- Tableau de bord admin
- Gestion des vendeurs
- Multi-langue
- Optimisation SEO

## Technologies utilisées

- **Laravel 12** - Framework PHP
- **Tailwind CSS** - Framework CSS
- **Alpine.js** - Framework JavaScript léger
- **Font Awesome** - Icônes
- **MySQL/PostgreSQL** - Base de données

## Licence

Ce projet est un projet éducatif. Libre d'utilisation et de modification.

## Support

Pour toute question ou problème, veuillez créer une issue sur le repository.# Shopme
