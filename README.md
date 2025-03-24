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

## 📂 Estrutura de Arquivos (Organizada)  

```plaintext
.
├── frontend/                  # Interface web
│   ├── css/                   # Estilos (style.css)
│   ├── js/                    # Scripts JavaScript
│   ├── fonts/                 # Fontes customizadas
│   └── img/                   # Imagens estáticas
│
├── backend/                   # Lógica PHP
│   ├── rds/                   # Integração com AWS RDS
│   │   ├── rds.php            # Conexão principal
│   │   ├── rds-config.php     # Configurações
│   │   └── rds-read-data.php  # Leitura de dados
│   │
│   ├── uploads/               # Gerenciamento de arquivos
│   │   ├── upload_handler.php # Processamento
│   │   └── list_files.php     # Listagem
│   │
│   ├── db-update.php          # Atualizações no banco
│   ├── load.php               # Monitoramento de CPU
│   └── put-cpu-load.php       # Registro de carga
│
├── k8s/                       # Configuração Kubernetes
│   ├── 1-pv.yaml              # Persistent Volume
│   ├── 2-pvc.yaml             # Volume Claim
│   └── 3-app.yaml             # Deployment
│
├── sql/                       # Scripts SQL/migrações
├── Dockerfile                 # Configuração do container
├── custom.apache.conf         # VirtualHost Apache
└── README.md                  # Documentação