# Talakay — Local Community Discussion Platform

Talakay is a lightweight, PHP-based community discussion platform designed for small towns and local neighborhoods. It provides a Reddit-style feed, topic-based rooms, and threaded comments stored in JSON files so it can run without a full database.

## What This Project Is About

Talakay aims to be a simple, self-hosted place for residents to share local updates, report issues (traffic, public safety), post about community events, and coordinate local initiatives like cleanups and pet adoption.

## Why It's Useful

- Easy to deploy: no database required (data stored in `data/*.json`).
- Focused on local context: rooms/topics for traffic, pets, environment, health, local businesses and public safety.
- Small footprint: PHP + single CSS file; ideal for small community projects or prototypes.

## Target Audience

- Local government volunteers and community organizers
- Small neighborhood groups
- Developers building a lightweight community demo or prototype
- Teachers or students learning basic PHP web apps

## Features

- Community feed with topic filters
- Room pages (e.g., Traffic, Stray Pets, Environment)
- Create posts and threaded replies (stored in `data/posts.json`)
- Simple user session handling (profiles, login/register pages)
- Responsive, Reddit-like UI implemented with plain CSS

## Quick Start (Development / Local)

Prerequisites:
- PHP 7.4+ installed
- A modern web browser

Start a local PHP server:

```powershell
cd 'C:\Users\lance\Talakay 2.0\Talakay'
php -S localhost:8000
```

Open http://localhost:8000/ in your browser.

Ensure the `data/` directory is writable by PHP.

## Installation (Production)

1. Place the project files on a PHP-enabled web server.
2. Ensure `data/` is writable by PHP (e.g., `chmod 775 data` on Linux).
3. Point your webserver to the project root or use a virtual host.

## How to Use

- Open the site and register or log in using the `Register` page.
- Use the left sidebar on the Community page to create posts or navigate room pages.
- Click a post's `Comment` button to reveal a reply form; replies are stored in `data/posts.json`.
- Topics are available via the Community discover controls or by visiting room pages under `rooms/`.

## Data Format

- Posts and replies are stored in `data/posts.json` as an array of post objects.
- Each post object includes fields like `id`, `author`, `author_email`, `topic`, `title`, `body`, `created_at`, and `replies` (an array of reply objects).
- Reply objects include `id`, `parent_id`, `author`, `author_email`, `body`, and `created_at`.

If you need to seed or edit data manually, edit `data/posts.json` carefully and keep valid JSON.

## Development Notes

- Key files:
	- `community.php` — main community feed and post creation
	- `rooms/*.php` — topic-specific pages
	- `models/PostRepository.php` — load/save helpers for JSON data and methods `addPost` / `addReply`
	- `includes/init.php`, `includes/nav.php`, `includes/footer.php` — shared includes
	- `assets/style.css` — global styles

- The app intentionally uses JSON files for simplicity. For production, replace `PostRepository` with a database-backed implementation.

## Troubleshooting

- If replying or posting fails, check file permissions on the `data/` folder and make sure PHP can write to it.
- If JavaScript reply toggles don't work, open browser DevTools Console for errors; footer scripts are in `includes/footer.php`.

## Contributing

Contributions welcome. Typical improvements:
- Bug fixes and UI enhancements
- Database backend (SQLite, MySQL)
- Tests and CI/CD configuration

Please open an issue and submit a pull request.

## License

No license included. Add one (e.g., MIT) if open-sourcing this project.

## Contact

For questions or issues, open an issue in the repository.
