# Pano

Pano is a lightweight PHP nano-framework designed as a minimal execution foundation.

It provides a small, explicit runtime that allows developers to build any architecture on top of it.

---

# What Pano Is

- a minimal execution foundation
- a low-level runtime structure
- a base for building custom frameworks
- a modular PHP system

---

# What Pano Is NOT

- a full-stack framework
- an opinionated application framework
- a batteries-included ecosystem
- a replacement for Laravel or similar frameworks

---

# When to Use Pano

Use Pano when you need:

- full control over application architecture
- a custom framework design
- a minimal runtime without imposed structure
- a base layer for system-level design

---

# When NOT to Use Pano

Avoid Pano if you need:

- rapid application scaffolding
- prebuilt authentication systems
- ORM-driven development
- convention-based frameworks
- beginner-friendly structure

---

# Key Concepts

Pano introduces a minimal set of runtime concepts:

- Kernel
- Foundation
- Modules
- Handlers
- Interceptors

These concepts are intentionally low-level and composable.

---

# Quick Start

```bash
composer install
php -S localhost:8000
```

---

# Minimal Example

```php
// bootstrap example (simplified)

$app = new \Pano\Foundation\Boot();
$app->run();
```

---

# Project Structure

```text
project/
├── config
├── src
│   ├── Kernel
│   ├── Enum
│   ├── Foundation
│   └── Modules
```

---

# Documentation

- [DOCUMENTATION.md](DOCUMENTATION.md) → complete developer guide and API reference
- [MANIFESTO.md](MANIFESTO.md) → philosophy and principles
- [ARCHITECTURE.md](ARCHITECTURE.md) → system design and runtime model

---

# Version Compatibility

- PHP 8.x is the baseline runtime
- Pano evolves conservatively to maintain stability

---

# Contribution

Before contributing:

- read ARCHITECTURE.md
- respect Kernel boundaries
- avoid introducing hidden behavior
- keep runtime explicit and predictable

---

# Status

Pano is a low-level framework foundation intended for advanced use cases.

You are free. Pano should never think or make decisions on behalf of developers.

---

# License

MIT