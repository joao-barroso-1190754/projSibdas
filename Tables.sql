-- 1. Create independent table: Localizações
CREATE TABLE localizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edificio VARCHAR(100) NOT NULL,
    piso VARCHAR(50),
    servico_departamento VARCHAR(100) NOT NULL,
    sala_gabinete VARCHAR(100),
    apagado BOOLEAN DEFAULT FALSE
);

-- 2. Create independent table: Fornecedores
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(150) NOT NULL,
    nif VARCHAR(20) UNIQUE,
    contacto_telefonico VARCHAR(20),
    email VARCHAR(100),
    morada TEXT,
    website VARCHAR(150),
    pessoa_contacto VARCHAR(100),
    telefone_contacto VARCHAR(20),
    tipo_fornecedor VARCHAR(100),
    observacoes TEXT,
    apagado BOOLEAN DEFAULT FALSE
);

-- 3. Create core table: Equipamentos (Depends on Localizações)
CREATE TABLE equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50) NOT NULL UNIQUE, 
    designacao VARCHAR(150) NOT NULL,
    categoria VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    fabricante VARCHAR(100),
    data_aquisicao DATE,
    ano_fabrico YEAR,
    custo_aquisicao DECIMAL(10, 2),
    tipo_entrada VARCHAR(50),
    estado VARCHAR(50) NOT NULL,
    criticidade VARCHAR(50),
    observacoes TEXT,
    apagado BOOLEAN DEFAULT FALSE,
    localizacao_id INT NOT NULL, -- Business rule: must have a location 
    parent_id INT DEFAULT NULL,
    
    FOREIGN KEY (localizacao_id) REFERENCES localizacoes(id) ON DELETE RESTRICT,
    FOREIGN KEY (parent_id) REFERENCES equipamentos(id) ON DELETE SET NULL,
    
    UNIQUE (fabricante, modelo, numero_serie)
);

-- 4. Create junction table: Equipamento_Fornecedor (Depends on Equipamentos & Fornecedores)
CREATE TABLE equipamento_fornecedor (
    equipamento_id INT,
    fornecedor_id INT,
    tipo_relacao VARCHAR(100), -- e.g., "Assistência Técnica", "Distribuidor" 
    PRIMARY KEY (equipamento_id, fornecedor_id, tipo_relacao),
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE CASCADE
);

-- Create table for Users (Utilizadores)
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    perfil ENUM('Normal', 'Tecnico', 'Admin') NOT NULL DEFAULT 'Normal',
    password VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE logs_equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipamento_id INT NOT NULL,
    utilizador_id INT NOT NULL,
    acao VARCHAR(255) NOT NULL,
    data_registo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id),
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id)
);