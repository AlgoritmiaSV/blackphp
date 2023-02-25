-- Actualizaciones de base de datos en el mes de octubre de 2022
-- Por: Edwin Fajardo
-- 2023-02-23
-- Se eliminan las llaves únicas por no ser compatibles con borrado lógico en SQL Server
ALTER TABLE blackphp.dbo.users DROP CONSTRAINT users$entity_nickname;
ALTER TABLE blackphp.dbo.users DROP CONSTRAINT users$entity_email;
-- inabve:blackphp
-- teleinf:blackphp
