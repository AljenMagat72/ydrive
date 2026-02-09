# Y Drive App Mono repo

## What's inside?

This Turborepo includes the following packages/apps/services

- `apps`: web applications 
- `services`: web services
- `packages`: shared packages

### Apps Services and Packages

#### Apps
- `admin-portal`: y drive admin portal
- `driver-portal`: y drive drivers portal
- `landing`: y drive home page

#### Services
- `api`: y drive api service
- `analytics-scraper`: driver analytics scraper

## Deploying

production:
running `bun run deploy` will rebase main with dev
push to main branch

dev:
push to dev branch

If creating a hotfix, you'll need to merge your changes from main back to dev