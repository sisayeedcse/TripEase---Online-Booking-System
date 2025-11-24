# GitHub Actions Workflows

This directory contains CI/CD workflows for the TripEase project.

## Workflows Overview

### 1. **ci-cd.yml** - Main CI/CD Pipeline

**Triggers:** Push to main/develop, Pull Requests  
**Purpose:** Complete continuous integration and deployment pipeline

**Jobs:**

- **code-quality**: Validates code syntax, runs linters
- **security-scan**: Scans for vulnerabilities and hardcoded secrets
- **test**: Runs PHPUnit tests across multiple PHP versions
- **build**: Creates deployment package
- **deploy-staging**: Deploys to staging environment (develop branch)
- **deploy-production**: Deploys to production (main branch)
- **performance-test**: Runs Lighthouse CI on staging
- **rollback**: Automatic rollback on deployment failure

### 2. **code-review.yml** - Automated Code Review

**Triggers:** Pull Requests  
**Purpose:** Automated code quality checks on PRs

**Checks:**

- Debug statements (var_dump, die, exit)
- Short PHP tags
- File permissions
- SQL injection patterns
- TODO/FIXME comments

### 3. **database-backup.yml** - Database Backup

**Triggers:** Daily at 2 AM UTC, Manual  
**Purpose:** Automated database backups

**Features:**

- Creates MySQL dumps
- Compresses backups (gzip)
- Uploads to S3/Cloud storage
- Cleans old backups (30-day retention)
- Sends notifications

### 4. **security-scan.yml** - Security Scanning

**Triggers:** Weekly on Mondays, Push to main, Manual  
**Purpose:** Comprehensive security scanning

**Scans:**

- Dependency vulnerabilities (Composer Audit)
- Code security (CodeQL)
- Hardcoded secrets detection
- File upload security validation
- Vulnerability scanning (Trivy)

## Setup Instructions

### 1. Repository Secrets

Add these secrets in GitHub Settings → Secrets and variables → Actions:

#### Production Deployment

```
PRODUCTION_HOST          - Production server hostname/IP
PRODUCTION_USERNAME      - SSH username
PRODUCTION_SSH_KEY       - SSH private key for authentication
PRODUCTION_DB_HOST       - Production database host
PRODUCTION_DB_NAME       - Database name
PRODUCTION_DB_USER       - Database username
PRODUCTION_DB_PASS       - Database password
```

#### Staging Deployment

```
STAGING_FTP_SERVER       - Staging FTP server
STAGING_FTP_USERNAME     - FTP username
STAGING_FTP_PASSWORD     - FTP password
```

#### Cloud Storage (for backups)

```
AWS_ACCESS_KEY_ID        - AWS access key
AWS_SECRET_ACCESS_KEY    - AWS secret key
AWS_REGION               - AWS region (e.g., us-east-1)
S3_BACKUP_BUCKET         - S3 bucket name for backups
```

#### Notifications

```
SLACK_WEBHOOK            - Slack webhook URL for notifications
```

### 2. Environment Configuration

Create environment configurations in GitHub:

1. Go to Settings → Environments
2. Create two environments:

   - **staging** (for develop branch)
   - **production** (for main branch with protection rules)

3. Add environment-specific variables and secrets

### 3. Branch Protection

Configure branch protection rules:

**Main Branch:**

- Require pull request reviews
- Require status checks to pass (tests, code-quality)
- Require branches to be up to date
- Include administrators

**Develop Branch:**

- Require status checks to pass
- Allow force pushes (for development)

### 4. Database Schema

Ensure `database/schema.sql` is up to date with:

- All table definitions
- Default admin account
- Sample data (optional)

## Usage

### Automatic Triggers

**On Push to `main`:**

- Runs full CI/CD pipeline
- Deploys to production (if tests pass)
- Creates GitHub release
- Sends notifications

**On Push to `develop`:**

- Runs CI/CD pipeline
- Deploys to staging
- Runs performance tests

**On Pull Request:**

- Runs code quality checks
- Runs automated code review
- Runs tests
- Comments on PR with results

**Daily (2 AM UTC):**

- Database backup

**Weekly (Mondays 9 AM UTC):**

- Security scans

### Manual Triggers

Run workflows manually from the Actions tab:

1. Go to Actions → Select workflow
2. Click "Run workflow"
3. Select branch
4. Click "Run workflow" button

### Deployment Process

**To Staging:**

```bash
git checkout develop
git commit -m "Your changes"
git push origin develop
# Automatically deploys to staging
```

**To Production:**

```bash
git checkout main
git merge develop
git push origin main
# Automatically deploys to production
```

## Monitoring

### View Workflow Status

1. Go to repository → Actions tab
2. Click on workflow run to see details
3. Review logs for each job

### Notifications

Configure Slack notifications to receive alerts for:

- ✅ Successful deployments
- ❌ Failed deployments
- 🔒 Security vulnerabilities
- 💾 Backup completions

### Artifacts

Workflow artifacts are stored for 30 days:

- Build packages
- Test results
- Security reports
- Code coverage reports

Access artifacts:

1. Go to workflow run
2. Scroll to "Artifacts" section
3. Download required artifact

## Troubleshooting

### Common Issues

**1. Tests Failing:**

- Check MySQL service is running
- Verify database schema is correct
- Check PHP version compatibility

**2. Deployment Failing:**

- Verify SSH keys are correct
- Check server permissions
- Ensure file paths are correct

**3. Build Failing:**

- Check Composer dependencies
- Verify PHP syntax
- Review error logs in Actions tab

**4. Database Backup Failing:**

- Verify database credentials
- Check S3 bucket permissions
- Ensure AWS CLI is configured

### Debug Mode

Enable debug logging:

1. Go to Settings → Secrets
2. Add secret: `ACTIONS_STEP_DEBUG = true`
3. Add secret: `ACTIONS_RUNNER_DEBUG = true`
4. Re-run workflow

### Rollback Procedure

**Manual Rollback:**

```bash
# SSH to production server
ssh user@production-server

# Navigate to project
cd /var/www/tripease

# Restore from backup
cp -r ../tripease.backup/* .

# Clear cache
php artisan cache:clear  # if applicable

# Restart services
sudo systemctl restart apache2
```

**Automatic Rollback:**

The workflow includes automatic rollback on deployment failure. It will:

1. Detect deployment failure
2. Restore from previous backup
3. Notify team via Slack

## Best Practices

### Before Merging to Main

1. ✅ All tests pass
2. ✅ Code review approved
3. ✅ Staging deployment successful
4. ✅ No security vulnerabilities
5. ✅ Documentation updated

### Database Changes

1. Test migrations locally
2. Deploy to staging first
3. Backup production database
4. Run migrations in production
5. Verify data integrity

### Secrets Management

- Never commit secrets to repository
- Rotate secrets regularly
- Use environment-specific secrets
- Audit secret access

### Monitoring

- Check workflow status daily
- Review security scan reports weekly
- Monitor deployment success rates
- Track test coverage trends

## Customization

### Modify PHP Versions

Edit `.github/workflows/ci-cd.yml`:

```yaml
matrix:
  php-version: ["7.4", "8.0", "8.1", "8.2"]
```

### Change Deployment Method

Replace FTP with SFTP/rsync/Git in deploy jobs:

```yaml
- name: Deploy via Git
  run: |
    git push production main
```

### Add Additional Tests

Add more test jobs in `ci-cd.yml`:

```yaml
integration-tests:
  name: Integration Tests
  runs-on: ubuntu-latest
  steps:
    # Your integration test steps
```

### Custom Notifications

Add email notifications:

```yaml
- name: Send Email
  uses: dawidd6/action-send-mail@v3
  with:
    server_address: smtp.gmail.com
    server_port: 465
    username: ${{ secrets.EMAIL_USERNAME }}
    password: ${{ secrets.EMAIL_PASSWORD }}
    subject: Deployment Status
    body: Deployment completed!
    to: team@tripease.com
```

## Support

For issues with workflows:

1. Check Actions tab for error logs
2. Review workflow configuration
3. Consult GitHub Actions documentation
4. Contact DevOps team

## Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PHP CI/CD Best Practices](https://www.php.net/manual/en/features.commandline.php)
- [Security Best Practices](https://docs.github.com/en/code-security)

---

**Last Updated:** November 24, 2025  
**Maintained by:** TripEase DevOps Team
