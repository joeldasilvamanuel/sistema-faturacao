-- Arquivo: supermercado-faturacao/db/database.sql

-- 1. Criação da Base de Dados
CREATE DATABASE IF NOT EXISTS supermercado_faturacao
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE supermercado_faturacao;

-- 2. Tabela de Roles (Papéis/Cargos)
CREATE TABLE roles (
    id_role INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome_role VARCHAR(100) NOT NULL UNIQUE
);

-- 3. Tabela de Utilizadores
CREATE TABLE utilizadores (
    id_utilizador INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome_utilizador VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,     -- Palavra-passe criptografada (bcrypt)
    id_role INT(11) NOT NULL,                -- FK para roles
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilizadores_roles FOREIGN KEY (id_role) REFERENCES roles(id_role)
);

-- 4. Inserção dos Papéis
INSERT INTO roles (nome_role) VALUES
('Operador de Caixa'),
('Fiscal de Caixa'),
('Gerente do Supermercado'),
('Equipa de Contabilidade'),
('Administrador do Sistema'),
('Gestor de Compras/Estoque'),
('Equipa de Atendimento ao Cliente');

-- 5. Inserção de Utilizador ADMIN de Teste
-- Login: admin
-- Password: admin123 (hash bcrypt)
-- NOTA: o id_role precisa corresponder ao "Administrador do Sistema"
INSERT INTO utilizadores (nome_utilizador, password_hash, id_role) 
VALUES (
    'admin',
    '$2y$10$tM2XyR0G4F1u.xN2pC0k9.iM.ePj/lqH4q/yFzN8bH9nJ7pL5wG2S',
    (SELECT id_role FROM roles WHERE nome_role = 'Administrador do Sistema')
);
