# Contributing to MINSU Alumni Management System

Thank you for your interest in contributing to the MINSU Alumni Management System!

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/alumni-system.git`
3. Create a feature branch: `git checkout -b feature/your-feature-name`
4. Make your changes
5. Test thoroughly
6. Commit with clear messages: `git commit -m "Add: feature description"`
7. Push to your fork: `git push origin feature/your-feature-name`
8. Open a Pull Request

## Development Guidelines

### Code Style

**PHP:**
- Follow PSR-12 coding standards
- Use type declarations where possible
- Document functions with PHPDoc comments
- Keep functions focused and single-purpose

**JavaScript:**
- Use ES6+ features
- Use meaningful variable names
- Add JSDoc comments for complex functions
- Keep functions pure when possible

**CSS:**
- Use BEM naming convention
- Keep selectors specific but not overly nested
- Use CSS custom properties for theming
- Mobile-first responsive design

### Commit Messages

Use conventional commit format:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting, etc.)
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `chore:` Maintenance tasks

Example: `feat: add email notification for event reminders`

### Testing

- Test all new features thoroughly
- Verify existing functionality still works
- Test on multiple browsers (Chrome, Firefox, Safari, Edge)
- Test responsive design on mobile devices
- Check for console errors

### Database Changes

- Create migration files for schema changes
- Name migrations with timestamp: `007_description.sql`
- Include both UP and DOWN migrations when possible
- Test migrations on a fresh database

### Security

- Never commit sensitive data (.env files, passwords, API keys)
- Sanitize all user inputs
- Use prepared statements for database queries
- Validate and escape output
- Follow OWASP security guidelines

## Pull Request Process

1. Update README.md if needed
2. Update DEPLOYMENT.md for deployment changes
3. Ensure all tests pass
4. Request review from maintainers
5. Address review feedback
6. Wait for approval and merge

## Code Review Checklist

- [ ] Code follows project style guidelines
- [ ] No sensitive data in commits
- [ ] Functions are documented
- [ ] Changes are tested
- [ ] No console.log() or var_dump() left in code
- [ ] Database queries use prepared statements
- [ ] Error handling is implemented
- [ ] Responsive design works on mobile

## Questions?

If you have questions, please open an issue or contact the maintainers.

## License

By contributing, you agree that your contributions will be licensed under the same license as the project.
