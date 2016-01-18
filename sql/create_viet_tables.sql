CREATE TABLE engvietword (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(11) NOT NULL,
    english VARCHAR(255) NOT NULL,
    pos VARCHAR(100) NOT NULL,
    vietnamese VARCHAR(255) NOT NULL,
    KEY identifierIndex (identifier),
    KEY englishIndex (english)
);

CREATE TABLE engvietexample (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(11) NOT NULL,
    english VARCHAR(255) NOT NULL,
    vietnamese VARCHAR(255) NOT NULL,
    KEY identifierIndex (identifier)
);
