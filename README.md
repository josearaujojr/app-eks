# app-eks

Projeto de uma aplicação web PHP com integração a serviços AWS (como EKS, EC2, ECR, LOAD BALANCER, NGINX, SECRET MANAGER), Apache como servidor web, e deploy em Kubernetes.

## 🛠️ Tecnologias
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Banco de Dados**: MySQL (via AWS RDS)
- **Infraestrutura**: 
  - Docker (containerização)
  - Kubernetes (orquestração)
  - AWS (RDS, ECR, EKS)
- **Web Server**: Apache

## 📂 Estrutura de Arquivos

├── html/ # Frontend (HTML/CSS/JS)
├── js/ # Scripts JavaScript
├── sql/ # Arquivos SQL
├── php/ # Backend PHP (RDS, uploads, etc.)
│ ├── db-update.php
│ ├── rds.php
│ ├── upload_handler.php
│ └── ...
├── k8s/ # Configurações Kubernetes
│ ├── 1-pv.yaml
│ ├── 2-pvc.yaml
│ └── 3-app.yaml
├── Dockerfile # Containerização
├── custom.apache.conf # Configuração do Apache
└── README.md