# app-eks

Projeto de uma aplicação web PHP com deploy automatizado em um cluster Kubernetes (AWS EKS). A aplicação demonstra a integração com diversos serviços da AWS, como S3 para armazenamento de arquivos, RDS para banco de dados, e EFS para armazenamento persistente, com um pipeline de CI/CD utilizando GitLab CI.

## Visão Geral da Arquitetura

O fluxo de deploy e a arquitetura da aplicação em execução são os seguintes:

1.  **CI/CD com GitLab:** Ao fazer um push para a branch `main`, o GitLab CI é acionado.
2.  **Build & Push:** O pipeline constrói uma imagem Docker da aplicação.
3.  **ECR (Elastic Container Registry):** A imagem Docker é enviada para um repositório no AWS ECR.
4.  **Deploy no EKS:** O pipeline se conecta ao cluster Kubernetes (EKS) e aplica os manifestos de deploy.
5.  **EKS (Elastic Kubernetes Service):** O EKS orquestra os contêineres da aplicação. A imagem mais recente é puxada do ECR.
6.  **Execução:**
    *   O tráfego de entrada é gerenciado por um AWS Load Balancer, que direciona para os pods da aplicação.
    *   A aplicação PHP (executando em um servidor Apache) lida com as requisições.
    *   Uploads de arquivos são armazenados em um **AWS S3 Bucket**.
    *   Dados persistentes da aplicação (como logs) são armazenados em um **AWS EFS (Elastic File System)**, montado nos pods através de Persistent Volumes.
    *   Dados relacionais são gerenciados por um banco de dados **AWS RDS (MySQL)**.

## 🛠️ Tecnologias

-   **Aplicação**:
    -   **Frontend**: HTML, CSS, JavaScript (Bootstrap)
    -   **Backend**: PHP
-   **Servidor Web**: Apache
-   **Banco de Dados**: MySQL (provisionado via AWS RDS)
-   **Containerização**: Docker
-   **CI/CD**: GitLab CI
-   **Infraestrutura & Cloud (AWS)**:
    -   **Orquestração**: Amazon EKS (Elastic Kubernetes Service)
    -   **Registro de Imagens**: Amazon ECR (Elastic Container Registry)
    -   **Armazenamento de Objetos**: Amazon S3 (Simple Storage Service)
    -   **Armazenamento de Arquivos Persistentes**: Amazon EFS (Elastic File System)
    -   **Gerenciamento de Segredos**: AWS Secrets Manager (mencionado, mas a implementação atual usa variáveis de ambiente)
    -   **Load Balancer**: AWS Application Load Balancer (ALB)

## 📂 Estrutura de Arquivos

```plaintext
.
├── html/                      # Raiz do servidor web (código da aplicação)
│   ├── css/                   # Estilos (Bootstrap)
│   ├── js/                    # Scripts (jQuery, Bootstrap)
│   ├── list_files.php         # Página para listar arquivos do S3
│   ├── upload_handler.php     # Endpoint para upload de arquivos para o S3
│   └── ...                    # Outros arquivos PHP e HTML da aplicação
│
├── k8s/                       # Manifestos de configuração do Kubernetes
│   └── app/
│       ├── 1-pv.yaml          # PersistentVolume para EFS
│       ├── 2-pvc.yaml         # PersistentVolumeClaim para solicitar o storage
│       └── 3-deployment.yaml  # Deployment da aplicação (pods, réplicas, etc.)
│
├── .gitlab-ci.yml             # Definição do pipeline de CI/CD
├── Dockerfile                 # Instruções para construir a imagem Docker da aplicação
├── custom.apache.conf         # Configuração do VirtualHost do Apache
└── README.md                  # Esta documentação