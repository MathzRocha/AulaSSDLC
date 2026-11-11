# ============================================================================
#  Dockerfile - imagem base ANTIGA de propósito (para Trivy image scan)
# ============================================================================
#  Imagem desatualizada => muitas CVEs de SO + PHP antigo.
#  Scan:  trivy image lab-inseguro:latest
#         trivy config .          (IaC/Dockerfile misconfig)
# ----------------------------------------------------------------------------
FROM php:7.4-apache

# [VULN: boas práticas de container] roda como root, sem usuário dedicado.
RUN docker-php-ext-install pdo pdo_sqlite

COPY . /var/www/html/

# [VULN] permissões excessivas
RUN chmod -R 777 /var/www/html

EXPOSE 80
# HEALTHCHECK ausente, USER root mantido — flags para Trivy/Hadolint.
