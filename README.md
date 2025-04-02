<a id="readme-top"></a>

<!--
*** Thanks for checking out the Best-README-Template. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->

<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/issam-mhj/gamestore_api_v2">
    <img src="https://github.com/user-attachments/assets/0ae1b6d5-1a62-4b41-b2c7-c595a0460497" alt="Logo" width="80" height="80">
  </a>

<h3 align="center">GameXpress API</h3>

  <p align="center">
    An administrative API for an e-commerce platform (GameXpress), built with Laravel 11, providing RESTful endpoints for managing products, categories, users, orders, and payments.
    <br />
    <a href="https://github.com/issam-mhj/gamestore_api_v2"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/issam-mhj/gamestore_api_v2">View Demo</a>
    &middot;
    <a href="https://github.com/issam-mhj/gamestore_api_v2/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    &middot;
    <a href="https://github.com/issam-mhj/gamestore_api_v2/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->

## About The Project

The GameXpress API is a Laravel 11-based backend designed to manage an e-commerce platform, focusing on administrator functionalities. It provides a RESTful API for managing products, categories, users, orders, and payments. Authentication is handled using Laravel Sanctum, and role-based access control is implemented with Spatie Permission.

[![Product Name Screen Shot][product-screenshot]](https://github.com/user-attachments/assets/721b7fb3-e480-4809-9023-fd48b82b1f8c)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Built With

*   [Laravel 11](https://laravel.com/)
*   [PHP 8.2+](https://www.php.net/)
*   [MySQL](https://www.mysql.com/)
*   [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
*   [Spatie Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
*   [Stripe PHP](https://stripe.com/docs/libraries#php)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- GETTING STARTED -->

## Getting Started

To get a local copy up and running, follow these steps.

### Prerequisites

*   PHP >= 8.2
*   Composer
*   MySQL
*   Node.js and npm (for Vite)

### Installation

1.  Clone the repository:

    ```sh
    git clone https://github.com/issam-mhj/gamestore_api_v2.git
    cd issam-mhj-gamestore_api_v2
    ```

2.  Install Composer dependencies:

    ```sh
    composer install
    ```

3.  Copy the `.env.example` file to `.env` and configure your database connection:

    ```sh
    cp .env.example .env
    ```

    Edit the `.env` file to set your database credentials:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
    ```

4.  Generate an application key:

    ```sh
    php artisan key:generate
    ```

5.  Run database migrations and seed the database:

    ```sh
    php artisan migrate --seed
    ```

6.  Install npm dependencies:

    ```sh
    npm install
    ```

7.  Compile assets with Vite:

    ```sh
    npm run dev
    ```

8.  Serve the application:

    ```sh
    php artisan serve
    ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- USAGE EXAMPLES -->

## Usage

The API provides the following main endpoints:

*   **Authentication:**
    *   `POST /api/register`: Register a new user.
    *   `POST /api/login`: Log in an existing user.
    *   `POST /api/logout`: Log out the authenticated user.

*   **Admin Dashboard (requires `view_dashboard` permission):**
    *   `GET /api/v1/admin/dashboard`: Retrieve dashboard statistics.

*   **Product Management (requires `view_products`, `create_products`, `edit_products`, `delete_products` permissions):**
    *   `GET /api/products`: List all products.
    *   `POST /api/products`: Create a new product.
    *   `GET /api/products/{product}`: Retrieve a specific product.
    *   `PUT /api/products/{product}`: Update a product.
    *   `DELETE /api/products/{product}`: Delete a product (soft delete).
    *   `POST /api/products/{product}/restore`: Restore a soft-deleted product.
    *   `DELETE /api/products/{product}/hard-delete`: Permanently delete a product.

*   **Category Management (requires `view_categories`, `create_categories`, `edit_categories`, `delete_categories` permissions):**
    *   `GET /api/v1/admin/categories`: List all categories.
    *   `POST /api/v1/admin/categories`: Create a new category.
    *   `PUT /api/v1/admin/categories/{category}`: Update a category.
    *   `DELETE /api/v1/admin/categories/{category}`: Delete a category.

*   **User Management (requires `view_users`, `create_users`, `edit_users`, `delete_users` permissions):**
    *   `GET /api/v1/admin/users`: List all users.
    *   `POST /api/v1/admin/users`: Create a new user.
    *   `PUT /api/v1/admin/users/{user}`: Update a user.
    *   `DELETE /api/v1/admin/users/{user}`: Delete a user.

*   **Order Management (requires `view_users` permission):**
    *   `GET /api/v1/admin/orders`: List all orders.
    *   `GET /api/v1/admin/orders/{id}`: Show a specific order.
    *   `DELETE /api/v1/admin/orders/{id}`: Cancel an order.
    *   `PUT /api/v1/admin/orders/{id}/status`: Update the status of an order.

*   **Payment Management (requires `view_users` permission):**
    *   `GET /api/v1/admin/payments`: List all payments.

*   **Roles Management (requires `edit_users` permission):**
    *   `GET /api/roles`: List all roles.
    *   `POST /api/roles/create`: Create a new role.
    *   `GET /api/roles/{id}`: Show a specific role.
    *   `PUT /api/roles/edit/{id}`: Update a role.
    *   `DELETE /api/roles/delete/{id}`: Delete a role.
    *   `POST /api/assign-roles`: Assign roles to a user.
    *   `GET /api/users/{id}/roles`: Get roles of a specific user.

*   **Cart Management:**
    *   `POST /api/v2/cart/add`: Add an item to the cart.
    *   `POST /api/v2/cart/update`: Update an item in the cart.
    *   `DELETE /api/v2/cart/remove/{CartItem}`: Remove an item from the cart.
    *   `POST /api/v2/cart/clear`: Clear the cart.
    *   `GET /api/v2/cart/items`: Get all items in the cart.

*   **Checkout:**
    *   `POST /api/checkout`: Initiate the checkout process (requires authentication).
    *   `GET /api/success`: Success URL for Stripe checkout.
    *   `GET /api/cancel`: Cancel URL for Stripe checkout.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ROADMAP -->

## Roadmap

*   Implement Swagger documentation for API endpoints.
*   Add more comprehensive unit and feature tests.
*   Implement advanced search and filtering options for products and users.
*   Implement queue for sending notifications.

See the [open issues](https://github.com/issam-mhj/gamestore_api_v2/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Top contributors:

<a href="https://github.com/issam-mhj/gamestore_api_v2/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=issam-mhj/gamestore_api_v2" alt="contrib.rocks image" />
</a>

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTACT -->

## Contact

Issam Mhj - [issam.mhj@email.com](mailto:issam.mhj@email.com)

Project Link: [https://github.com/issam-mhj/gamestore_api_v2](https://github.com/issam-mhj/gamestore_api_v2)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

*   Laravel Documentation
*   Spatie Permission Package
*   Stripe PHP Library

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/issam-mhj/gamestore_api_v2.svg?style=for-the-badge
[contributors-url]: https://github.com/issam-mhj/gamestore_api_v2/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/issam-mhj/gamestore_api_v2.svg?style=for-the-badge
[forks-url]: https://github.com/issam-mhj/gamestore_api_v2/network/members
[stars-shield]: https://img.shields.io/github/stars/issam-mhj/gamestore_api_v2.svg?style=for-the-badge
[stars-url]: https://github.com/issam-mhj/gamestore_api_v2/stargazers
[issues-shield]: https://img.shields.io/github/issues/issam-mhj/gamestore_api_v2.svg?style=for-the-badge
[issues-url]: https://github.com/issam-mhj/gamestore_api_v2/issues
[license-shield]: https://img.shields.io/github/license/issam-mhj/gamestore_api_v2.svg?style=for-the-badge
[license-url]: https://github.com/issam-mhj/gamestore_api_v2/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/linkedin_username
[product-screenshot]: https://github.com/user-attachments/assets/721b7fb3-e480-4809-9023-fd48b82b1f8c
[Next.js]: https://img.shields.io/badge/next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white
[Next-url]: https://nextjs.org/
[React.js]: https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB
[React-url]: https://reactjs.org/
[Vue.js]: https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D
[Vue-url]: https://vuejs.org/
[Angular.io]: https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white
[Angular-url]: https://angular.io/
[Svelte.dev]: https://img.shields.io/badge/Svelte-4A4A55?style=for-the-badge&logo=svelte&logoColor=FF3
