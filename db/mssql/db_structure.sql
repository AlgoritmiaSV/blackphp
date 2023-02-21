
USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_accessadmin')      
     EXEC (N'CREATE SCHEMA db_accessadmin')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_backupoperator')      
     EXEC (N'CREATE SCHEMA db_backupoperator')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_datareader')      
     EXEC (N'CREATE SCHEMA db_datareader')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_datawriter')      
     EXEC (N'CREATE SCHEMA db_datawriter')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_ddladmin')      
     EXEC (N'CREATE SCHEMA db_ddladmin')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_denydatareader')      
     EXEC (N'CREATE SCHEMA db_denydatareader')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_denydatawriter')      
     EXEC (N'CREATE SCHEMA db_denydatawriter')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_owner')      
     EXEC (N'CREATE SCHEMA db_owner')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'db_securityadmin')      
     EXEC (N'CREATE SCHEMA db_securityadmin')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'dbo')      
     EXEC (N'CREATE SCHEMA dbo')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'guest')      
     EXEC (N'CREATE SCHEMA guest')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'INFORMATION_SCHEMA')      
     EXEC (N'CREATE SCHEMA INFORMATION_SCHEMA')                                   
 GO                                                               

USE blackphp
GO
 IF NOT EXISTS(SELECT * FROM sys.schemas WHERE [name] = N'sys')      
     EXEC (N'CREATE SCHEMA sys')                                   
 GO                                                               

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_elements'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_elements'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_elements]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_elements]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [element_id] smallint IDENTITY(4, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Clave del elemento'.
   */

   [element_key] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del elemento'.
   */

   [element_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre singular del elemento'.
   */

   [singular_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'M: Masculino, F: Femenino'.
   */

   [element_gender] nchar(1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Es un elemento único'.
   */

   [unique_element] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del módulo'.
   */

   [module_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del método para ver detalle'.
   */

   [method_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'El elemento se puede eliminar'.
   */

   [deletable] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre de la tabla'.
   */

   [table_name] nvarchar(64)  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_elements',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_elements'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_installers'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_installers'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_installers]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_installers]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [installer_id] int IDENTITY(2, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Usuario'.
   */

   [installer_nickname] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Resumen de contraseña'.
   */

   [installer_password] nchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del instalador'.
   */

   [installer_name] nvarchar(128)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Teléfono'.
   */

   [installer_phone] nvarchar(16)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Correo electrónico'.
   */

   [installer_email] nvarchar(64)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Hora y fecha de creación'.
   */

   [creation_time] datetime2(0)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Eliminado, inactivo, activo'.
   */

   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_installers',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_installers'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_methods'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_methods'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_methods]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_methods]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [method_id] int IDENTITY(6, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del módulo'.
   */

   [module_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del método'.
   */

   [method_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'URL del método (Nombre de la función PHP)'.
   */

   [method_url] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Ícono del método en el menú'.
   */

   [method_icon] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Descripción del método'.
   */

   [method_description] nvarchar(255)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Orden por defecto'.
   */

   [default_order] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Estado 0:inactivo, 1:activo'.
   */

   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_methods',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_methods'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_modules'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_modules'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_modules]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_modules]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [module_id] int IDENTITY(3, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del módulo'.
   */

   [module_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'URL del módulo'.
   */

   [module_url] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Ícono del módulo en el menú'.
   */

   [module_icon] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Tecla de acceso rápido'.
   */

   [module_key] nchar(1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Descripción del módulo'.
   */

   [module_description] nvarchar(255)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Orden por defecto'.
   */

   [default_order] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Estado 0:inactivo, 1:activo'.
   */

   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_modules',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_modules'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_options'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_options'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_options]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_options]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Llave primaria'.
   */

   [option_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Tipo de variable: 1: Booleana; 2: Valor'.
   */

   [option_type] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Clave de la opción'.
   */

   [option_key] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Descripción de la opción'.
   */

   [option_description] nvarchar(255)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Módulo en el que se realiza la configuración'.
   */

   [module_id] int  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Valor por defecto de la opción'.
   */

   [default_value] nvarchar(255)  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_options',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_options'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_themes'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'app_themes'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[app_themes]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[app_themes]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [theme_id] int IDENTITY(6, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del tema'.
   */

   [theme_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre de la carpeta pública'.
   */

   [theme_url] nvarchar(16)  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_themes',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_themes'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'browsers'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'browsers'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[browsers]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[browsers]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [browser_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Cadena completa User Agent enviada por el navegador'.
   */

   [user_agent] nvarchar(255)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre del navegador'.
   */

   [browser_name] nvarchar(16)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Versión del navegador'.
   */

   [browser_version] nvarchar(16)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Sistema operativo'.
   */

   [platform] nvarchar(16)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Primer usuario que lo registra'.
   */

   [creation_user] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Hora y fecha de registro'.
   */

   [creation_time] datetime2(0)  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.browsers',
        N'SCHEMA', N'dbo',
        N'TABLE', N'browsers'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entities'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'entities'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[entities]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[entities]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [entity_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre de la empresa'.
   */

   [entity_name] nvarchar(64)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Eslogan de la empresa'.
   */

   [entity_slogan] nvarchar(128)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Usuario principal (Superadministrador)'.
   */

   [admin_user] int  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Fecha actual de operaciones (En caso que difiera del sistema)'.
   */

   [entity_date] date  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Fecha de inicio de las operaciones'.
   */

   [entity_begin] date  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Subdominio (Para funcionamiento en línea)'.
   */

   [entity_subdomain] nvarchar(32)  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre de la App para instalación como PWA'.
   */

   [app_name] nvarchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Idioma por defecto de la entidad'.
   */

   [default_locale] nchar(5)  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario que instaló el sistema'.
   */

   [creation_installer] int  NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_installer] int  NULL,
   [installer_edition_time] datetime2(0)  NOT NULL,
   [edition_user] int  NULL,
   [user_edition_time] datetime2(0)  NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.entities',
        N'SCHEMA', N'dbo',
        N'TABLE', N'entities'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_methods'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'entity_methods'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[entity_methods]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[entity_methods]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [emethod_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la empresa'.
   */

   [entity_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del método'.
   */

   [method_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Orden en el que aoparecerá el método en el menú'.
   */

   [method_order] smallint  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.entity_methods',
        N'SCHEMA', N'dbo',
        N'TABLE', N'entity_methods'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_modules'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'entity_modules'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[entity_modules]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[entity_modules]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [emodule_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la empresa'.
   */

   [entity_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del módulo'.
   */

   [module_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Ubicación del módulo en el menú'.
   */

   [module_order] smallint  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.entity_modules',
        N'SCHEMA', N'dbo',
        N'TABLE', N'entity_modules'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_options'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'entity_options'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[entity_options]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[entity_options]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Llave primaria'.
   */

   [eoption_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la entidad'.
   */

   [entity_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la opción'.
   */

   [option_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Valor de la opción'.
   */

   [option_value] nvarchar(255)  NOT NULL,
   [creation_user] int  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_user] int  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.entity_options',
        N'SCHEMA', N'dbo',
        N'TABLE', N'entity_options'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_logs'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'user_logs'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[user_logs]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[user_logs]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [log_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario'.
   */

   [user_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del tipo de elemento'.
   */

   [element_id] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Acción realizada'.
   */

   [action_id] smallint  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Hora y fecha'.
   */

   [date_time] datetime2(0)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Enlace al elemento en cuestión'.
   */

   [element_link] int  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_logs',
        N'SCHEMA', N'dbo',
        N'TABLE', N'user_logs'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_methods'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'user_methods'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[user_methods]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[user_methods]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [umethod_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario'.
   */

   [user_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del método'.
   */

   [method_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Tipo de acceso'.
   */

   [access_type] tinyint  NOT NULL,
   [creation_user] int  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_user] int  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_methods',
        N'SCHEMA', N'dbo',
        N'TABLE', N'user_methods'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_modules'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'user_modules'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[user_modules]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[user_modules]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [umodule_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del módulo'.
   */

   [module_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario'.
   */

   [user_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Tipo de acceso al módulo'.
   */

   [access_type] int  NULL,
   [creation_user] int  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_user] int  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_modules',
        N'SCHEMA', N'dbo',
        N'TABLE', N'user_modules'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_recovery'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'user_recovery'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[user_recovery]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[user_recovery]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [urecovery_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario'.
   */

   [user_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Código de recuperación'.
   */

   [urecovery_code] nchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Fecha y hora de vencimiento'.
   */

   [expiration_time] datetime2(0)  NOT NULL,
   [creation_user] int  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_user] int  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_recovery',
        N'SCHEMA', N'dbo',
        N'TABLE', N'user_recovery'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_sessions'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'user_sessions'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[user_sessions]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[user_sessions]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [usession_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID del usuario'.
   */

   [user_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Sucursal en la que inició sesión'.
   */

   [branch_id] int  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Dirección IP desde donde se conecta'.
   */

   [ip_address] nvarchar(15)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Navegador que usa'.
   */

   [browser_id] int  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Fecha y hora'.
   */

   [date_time] datetime2(0)  NOT NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_sessions',
        N'SCHEMA', N'dbo',
        N'TABLE', N'user_sessions'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'users'  AND sc.name = N'dbo'  AND type in (N'U'))
BEGIN

  DECLARE @drop_statement nvarchar(500)

  DECLARE drop_cursor CURSOR FOR
      SELECT 'alter table '+quotename(schema_name(ob.schema_id))+
      '.'+quotename(object_name(ob.object_id))+ ' drop constraint ' + quotename(fk.name) 
      FROM sys.objects ob INNER JOIN sys.foreign_keys fk ON fk.parent_object_id = ob.object_id
      WHERE fk.referenced_object_id = 
          (
             SELECT so.object_id 
             FROM sys.objects so JOIN sys.schemas sc
             ON so.schema_id = sc.schema_id
             WHERE so.name = N'users'  AND sc.name = N'dbo'  AND type in (N'U')
           )

  OPEN drop_cursor

  FETCH NEXT FROM drop_cursor
  INTO @drop_statement

  WHILE @@FETCH_STATUS = 0
  BEGIN
     EXEC (@drop_statement)

     FETCH NEXT FROM drop_cursor
     INTO @drop_statement
  END

  CLOSE drop_cursor
  DEALLOCATE drop_cursor

  DROP TABLE [dbo].[users]
END 
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE 
[dbo].[users]
(

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la tabla'.
   */

   [user_id] int IDENTITY(1, 1)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'ID de la empresa'.
   */

   [entity_id] int  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Nombre completo del usuario'.
   */

   [user_name] nvarchar(64)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Usuario para inicio de sesión'.
   */

   [nickname] nvarchar(32)  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Correo electrónico'.
   */

   [email] nvarchar(64)  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Contraseña'.
   */

   [password] nchar(32)  NOT NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Tema de visualización del usuario'.
   */

   [theme_id] int  NULL,

   /*
   *   SSMA informational messages:
   *   M2SS0003: The following SQL clause was ignored during conversion: COMMENT 'Idioma del usuario'.
   */

   [locale] nchar(5)  NULL,
   [creation_user] int  NOT NULL,
   [creation_time] datetime2(0)  NOT NULL,
   [edition_user] int  NOT NULL,
   [edition_time] datetime2(0)  NOT NULL,
   [status] smallint  NULL
)
WITH (DATA_COMPRESSION = NONE)
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.users',
        N'SCHEMA', N'dbo',
        N'TABLE', N'users'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_elements_element_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_elements] DROP CONSTRAINT [PK_app_elements_element_id]
 GO



ALTER TABLE [dbo].[app_elements]
 ADD CONSTRAINT [PK_app_elements_element_id]
   PRIMARY KEY
   CLUSTERED ([element_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_installers_installer_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_installers] DROP CONSTRAINT [PK_app_installers_installer_id]
 GO



ALTER TABLE [dbo].[app_installers]
 ADD CONSTRAINT [PK_app_installers_installer_id]
   PRIMARY KEY
   CLUSTERED ([installer_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_methods_method_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_methods] DROP CONSTRAINT [PK_app_methods_method_id]
 GO



ALTER TABLE [dbo].[app_methods]
 ADD CONSTRAINT [PK_app_methods_method_id]
   PRIMARY KEY
   CLUSTERED ([method_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_modules_module_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_modules] DROP CONSTRAINT [PK_app_modules_module_id]
 GO



ALTER TABLE [dbo].[app_modules]
 ADD CONSTRAINT [PK_app_modules_module_id]
   PRIMARY KEY
   CLUSTERED ([module_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_options_option_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_options] DROP CONSTRAINT [PK_app_options_option_id]
 GO



ALTER TABLE [dbo].[app_options]
 ADD CONSTRAINT [PK_app_options_option_id]
   PRIMARY KEY
   CLUSTERED ([option_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_app_themes_theme_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[app_themes] DROP CONSTRAINT [PK_app_themes_theme_id]
 GO



ALTER TABLE [dbo].[app_themes]
 ADD CONSTRAINT [PK_app_themes_theme_id]
   PRIMARY KEY
   CLUSTERED ([theme_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_browsers_browser_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[browsers] DROP CONSTRAINT [PK_browsers_browser_id]
 GO



ALTER TABLE [dbo].[browsers]
 ADD CONSTRAINT [PK_browsers_browser_id]
   PRIMARY KEY
   CLUSTERED ([browser_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_entities_entity_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[entities] DROP CONSTRAINT [PK_entities_entity_id]
 GO



ALTER TABLE [dbo].[entities]
 ADD CONSTRAINT [PK_entities_entity_id]
   PRIMARY KEY
   CLUSTERED ([entity_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_entity_methods_emethod_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[entity_methods] DROP CONSTRAINT [PK_entity_methods_emethod_id]
 GO



ALTER TABLE [dbo].[entity_methods]
 ADD CONSTRAINT [PK_entity_methods_emethod_id]
   PRIMARY KEY
   CLUSTERED ([emethod_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_entity_modules_emodule_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[entity_modules] DROP CONSTRAINT [PK_entity_modules_emodule_id]
 GO



ALTER TABLE [dbo].[entity_modules]
 ADD CONSTRAINT [PK_entity_modules_emodule_id]
   PRIMARY KEY
   CLUSTERED ([emodule_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_entity_options_eoption_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[entity_options] DROP CONSTRAINT [PK_entity_options_eoption_id]
 GO



ALTER TABLE [dbo].[entity_options]
 ADD CONSTRAINT [PK_entity_options_eoption_id]
   PRIMARY KEY
   CLUSTERED ([eoption_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_user_logs_log_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[user_logs] DROP CONSTRAINT [PK_user_logs_log_id]
 GO



ALTER TABLE [dbo].[user_logs]
 ADD CONSTRAINT [PK_user_logs_log_id]
   PRIMARY KEY
   CLUSTERED ([log_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_user_methods_umethod_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[user_methods] DROP CONSTRAINT [PK_user_methods_umethod_id]
 GO



ALTER TABLE [dbo].[user_methods]
 ADD CONSTRAINT [PK_user_methods_umethod_id]
   PRIMARY KEY
   CLUSTERED ([umethod_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_user_modules_umodule_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[user_modules] DROP CONSTRAINT [PK_user_modules_umodule_id]
 GO



ALTER TABLE [dbo].[user_modules]
 ADD CONSTRAINT [PK_user_modules_umodule_id]
   PRIMARY KEY
   CLUSTERED ([umodule_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_user_recovery_urecovery_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[user_recovery] DROP CONSTRAINT [PK_user_recovery_urecovery_id]
 GO



ALTER TABLE [dbo].[user_recovery]
 ADD CONSTRAINT [PK_user_recovery_urecovery_id]
   PRIMARY KEY
   CLUSTERED ([urecovery_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_user_sessions_usession_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[user_sessions] DROP CONSTRAINT [PK_user_sessions_usession_id]
 GO



ALTER TABLE [dbo].[user_sessions]
 ADD CONSTRAINT [PK_user_sessions_usession_id]
   PRIMARY KEY
   CLUSTERED ([usession_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'PK_users_user_id'  AND sc.name = N'dbo'  AND type in (N'PK'))
ALTER TABLE [dbo].[users] DROP CONSTRAINT [PK_users_user_id]
 GO



ALTER TABLE [dbo].[users]
 ADD CONSTRAINT [PK_users_user_id]
   PRIMARY KEY
   CLUSTERED ([user_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_elements$element_key'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[app_elements] DROP CONSTRAINT [app_elements$element_key]
 GO



ALTER TABLE [dbo].[app_elements]
 ADD CONSTRAINT [app_elements$element_key]
 UNIQUE 
   NONCLUSTERED ([element_key] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_options$unique_key_module'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[app_options] DROP CONSTRAINT [app_options$unique_key_module]
 GO



ALTER TABLE [dbo].[app_options]
 ADD CONSTRAINT [app_options$unique_key_module]
 UNIQUE 
   NONCLUSTERED ([option_key] ASC, [module_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'browsers$user_agent'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[browsers] DROP CONSTRAINT [browsers$user_agent]
 GO



ALTER TABLE [dbo].[browsers]
 ADD CONSTRAINT [browsers$user_agent]
 UNIQUE 
   NONCLUSTERED ([user_agent] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entities$comp_subdomain'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[entities] DROP CONSTRAINT [entities$comp_subdomain]
 GO



ALTER TABLE [dbo].[entities]
 ADD CONSTRAINT [entities$comp_subdomain]
 UNIQUE 
   NONCLUSTERED ([entity_subdomain] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_methods$comp_method'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[entity_methods] DROP CONSTRAINT [entity_methods$comp_method]
 GO



ALTER TABLE [dbo].[entity_methods]
 ADD CONSTRAINT [entity_methods$comp_method]
 UNIQUE 
   NONCLUSTERED ([entity_id] ASC, [method_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_methods$unique_user_method'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[user_methods] DROP CONSTRAINT [user_methods$unique_user_method]
 GO



ALTER TABLE [dbo].[user_methods]
 ADD CONSTRAINT [user_methods$unique_user_method]
 UNIQUE 
   NONCLUSTERED ([user_id] ASC, [method_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_modules$unique_user_module'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[user_modules] DROP CONSTRAINT [user_modules$unique_user_module]
 GO



ALTER TABLE [dbo].[user_modules]
 ADD CONSTRAINT [user_modules$unique_user_module]
 UNIQUE 
   NONCLUSTERED ([module_id] ASC, [user_id] ASC)

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'users$entity_nickname'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[users] DROP CONSTRAINT [users$entity_nickname]
 GO



ALTER TABLE [dbo].[users]
 ADD CONSTRAINT [users$entity_nickname]
 UNIQUE 
   NONCLUSTERED ([entity_id] ASC, [nickname] ASC, [status] ASC)

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'users$entity_email'  AND sc.name = N'dbo'  AND type in (N'UQ'))
ALTER TABLE [dbo].[users] DROP CONSTRAINT [users$entity_email]
 GO



ALTER TABLE [dbo].[users]
 ADD CONSTRAINT [users$entity_email]
 UNIQUE 
   NONCLUSTERED ([entity_id] ASC, [email] ASC, [status] ASC)

GO


USE blackphp
GO
IF  EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc on so.schema_id = sc.schema_id WHERE so.name = N'AppOptionAfterInsert_AfterInsert'  AND sc.name=N'dbo'  AND type in (N'TR'))
 DROP TRIGGER [dbo].[AppOptionAfterInsert_AfterInsert]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

/*
*   SSMA informational messages:
*   M2SS0003: The following SQL clause was ignored during conversion:
*   DEFINER = `root`@`localhost`.
*/

CREATE TRIGGER dbo.AppOptionAfterInsert_AfterInsert
   ON dbo.app_options
    AFTER INSERT
   AS 
      BEGIN

         SET  NOCOUNT  ON

         SET  XACT_ABORT  ON

         /* column variables declaration*/
         DECLARE
            @new$option_id int, 
            @new$default_value nvarchar(255)

         DECLARE
             ForEachInsertedRowTriggerCursor CURSOR LOCAL FORWARD_ONLY READ_ONLY FOR 
               SELECT option_id, default_value
               FROM inserted

         OPEN ForEachInsertedRowTriggerCursor

         FETCH ForEachInsertedRowTriggerCursor
             INTO @new$option_id, @new$default_value

         WHILE @@fetch_status = 0
         
            BEGIN

               /*
               *   SSMA warning messages:
               *   M2SS0119: INSERT ... SELECT statement may fail in case the value for identity column is NULL.
               */

               /* trigger implementation: begin*/
               BEGIN

                  SET  IDENTITY_INSERT dbo.entity_options  ON

                  INSERT dbo.entity_options(
                     eoption_id, 
                     entity_id, 
                     option_id, 
                     option_value, 
                     creation_user, 
                     creation_time, 
                     edition_user, 
                     edition_time, 
                     status)
                     SELECT 
                        NULL, 
                        entities.entity_id, 
                        @new$option_id, 
                        @new$default_value, 
                        0, 
                        getdate(), 
                        0, 
                        getdate(), 
                        1
                     FROM dbo.entities

                  SET  IDENTITY_INSERT dbo.entity_options  OFF

               END
               /* trigger implementation: end*/

               FETCH ForEachInsertedRowTriggerCursor
                   INTO @new$option_id, @new$default_value

            END

         CLOSE ForEachInsertedRowTriggerCursor

         DEALLOCATE ForEachInsertedRowTriggerCursor

      END
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.app_options.AppOptionAfterInsert',
        N'SCHEMA', N'dbo',
        N'TABLE', N'app_options',
        N'TRIGGER', N'AppOptionAfterInsert_AfterInsert'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF  EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc on so.schema_id = sc.schema_id WHERE so.name = N'EntityAfterInsert_AfterInsert'  AND sc.name=N'dbo'  AND type in (N'TR'))
 DROP TRIGGER [dbo].[EntityAfterInsert_AfterInsert]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

/*
*   SSMA informational messages:
*   M2SS0003: The following SQL clause was ignored during conversion:
*   DEFINER = `root`@`localhost`.
*/

CREATE TRIGGER dbo.EntityAfterInsert_AfterInsert
   ON dbo.entities
    AFTER INSERT
   AS 
      BEGIN

         SET  NOCOUNT  ON

         SET  XACT_ABORT  ON

         /* column variables declaration*/
         DECLARE
            @new$entity_id int

         DECLARE
             ForEachInsertedRowTriggerCursor CURSOR LOCAL FORWARD_ONLY READ_ONLY FOR 
               SELECT entity_id
               FROM inserted

         OPEN ForEachInsertedRowTriggerCursor

         FETCH ForEachInsertedRowTriggerCursor
             INTO @new$entity_id

         WHILE @@fetch_status = 0
         
            BEGIN

               /*
               *   SSMA warning messages:
               *   M2SS0119: INSERT ... SELECT statement may fail in case the value for identity column is NULL.
               */

               /* trigger implementation: begin*/
               BEGIN

                  SET  IDENTITY_INSERT dbo.entity_options  ON

                  INSERT dbo.entity_options(
                     eoption_id, 
                     entity_id, 
                     option_id, 
                     option_value, 
                     creation_user, 
                     creation_time, 
                     edition_user, 
                     edition_time, 
                     status)
                     SELECT 
                        NULL, 
                        @new$entity_id, 
                        app_options.option_id, 
                        app_options.default_value, 
                        0, 
                        getdate(), 
                        0, 
                        getdate(), 
                        1
                     FROM dbo.app_options

                  SET  IDENTITY_INSERT dbo.entity_options  OFF

               END
               /* trigger implementation: end*/

               FETCH ForEachInsertedRowTriggerCursor
                   INTO @new$entity_id

            END

         CLOSE ForEachInsertedRowTriggerCursor

         DEALLOCATE ForEachInsertedRowTriggerCursor

      END
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.entities.EntityAfterInsert',
        N'SCHEMA', N'dbo',
        N'TABLE', N'entities',
        N'TRIGGER', N'EntityAfterInsert_AfterInsert'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF  EXISTS (select * from sys.objects so join sys.schemas sc on so.schema_id = sc.schema_id where so.name = N'available_methods' and sc.name=N'dbo' AND type in (N'V'))
 DROP VIEW [dbo].[available_methods]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

/*
*   SSMA informational messages:
*   M2SS0003: The following SQL clause was ignored during conversion:
*   ALGORITHM =  UNDEFINED.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   DEFINER = `root`@`localhost`.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   SQL SECURITY DEFINER.
*/

CREATE VIEW dbo.available_methods (
   [method_id], 
   [module_id], 
   [method_name], 
   [method_url], 
   [method_icon], 
   [method_description], 
   [default_order], 
   [status], 
   [method_order], 
   [id], 
   [label], 
   [entity_id], 
   [user_id])
AS 
   SELECT 
      am.method_id AS method_id, 
      am.module_id AS module_id, 
      am.method_name AS method_name, 
      am.method_url AS method_url, 
      am.method_icon AS method_icon, 
      am.method_description AS method_description, 
      am.default_order AS default_order, 
      am.status AS status, 
      im.method_order AS method_order, 
      am.method_id AS id, 
      am.method_name AS label, 
      im.entity_id AS entity_id, 
      um.user_id AS user_id
   FROM (((dbo.app_methods  AS am 
      CROSS JOIN dbo.user_methods  AS um) 
      CROSS JOIN dbo.entity_methods  AS im) 
      CROSS JOIN dbo.users  AS u)
   WHERE 
      um.method_id = am.method_id AND 
      um.status = 1 AND 
      im.method_id = am.method_id AND 
      im.status = 1 AND 
      u.entity_id = im.entity_id AND 
      u.user_id = um.user_id
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.available_methods',
        N'SCHEMA', N'dbo',
        N'VIEW', N'available_methods'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF  EXISTS (select * from sys.objects so join sys.schemas sc on so.schema_id = sc.schema_id where so.name = N'available_modules' and sc.name=N'dbo' AND type in (N'V'))
 DROP VIEW [dbo].[available_modules]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

/*
*   SSMA informational messages:
*   M2SS0003: The following SQL clause was ignored during conversion:
*   ALGORITHM =  UNDEFINED.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   DEFINER = `root`@`localhost`.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   SQL SECURITY DEFINER.
*/

CREATE VIEW dbo.available_modules (
   [module_id], 
   [module_name], 
   [module_url], 
   [module_icon], 
   [module_key], 
   [module_description], 
   [default_order], 
   [status], 
   [access_type], 
   [entity_id], 
   [user_id], 
   [module_order])
AS 
   SELECT TOP (9223372036854775807) 
      m.module_id AS module_id, 
      m.module_name AS module_name, 
      m.module_url AS module_url, 
      m.module_icon AS module_icon, 
      m.module_key AS module_key, 
      m.module_description AS module_description, 
      m.default_order AS default_order, 
      m.status AS status, 
      um.access_type AS access_type, 
      em.entity_id AS entity_id, 
      u.user_id AS user_id, 
      em.module_order AS module_order
   FROM (((dbo.entity_modules  AS em 
      CROSS JOIN dbo.app_modules  AS m) 
      CROSS JOIN dbo.user_modules  AS um) 
      CROSS JOIN dbo.users  AS u)
   WHERE 
      m.module_id = em.module_id AND 
      em.status = 1 AND 
      um.module_id = m.module_id AND 
      um.status = 1 AND 
      u.entity_id = em.entity_id AND 
      u.user_id = um.user_id
      ORDER BY em.module_order
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.available_modules',
        N'SCHEMA', N'dbo',
        N'VIEW', N'available_modules'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF  EXISTS (select * from sys.objects so join sys.schemas sc on so.schema_id = sc.schema_id where so.name = N'user_data' and sc.name=N'dbo' AND type in (N'V'))
 DROP VIEW [dbo].[user_data]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

/*
*   SSMA informational messages:
*   M2SS0003: The following SQL clause was ignored during conversion:
*   ALGORITHM =  UNDEFINED.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   DEFINER = `root`@`localhost`.
*   M2SS0003: The following SQL clause was ignored during conversion:
*   SQL SECURITY DEFINER.
*/

CREATE VIEW dbo.user_data (
   [user_id], 
   [entity_id], 
   [user_name], 
   [nickname], 
   [email], 
   [password], 
   [theme_id], 
   [locale], 
   [creation_user], 
   [creation_time], 
   [edition_user], 
   [edition_time], 
   [status], 
   [last_login])
AS 
   SELECT 
      u.user_id AS user_id, 
      u.entity_id AS entity_id, 
      u.user_name AS user_name, 
      u.nickname AS nickname, 
      u.email AS email, 
      u.password AS password, 
      u.theme_id AS theme_id, 
      u.locale AS locale, 
      u.creation_user AS creation_user, 
      u.creation_time AS creation_time, 
      u.edition_user AS edition_user, 
      u.edition_time AS edition_time, 
      u.status AS status, 
      ls.last_login AS last_login
   FROM (dbo.users  AS u 
      LEFT JOIN 
      (
         SELECT TOP (9223372036854775807) user_sessions.user_id AS user_id, max(user_sessions.date_time) AS last_login
         FROM dbo.user_sessions
         GROUP BY user_sessions.user_id
            ORDER BY user_sessions.user_id
      )  AS ls 
      ON (ls.user_id = u.user_id))
   WHERE u.status = 1
GO
BEGIN TRY
    EXEC sp_addextendedproperty
        N'MS_SSMA_SOURCE', N'blackphpMigration.user_data',
        N'SCHEMA', N'dbo',
        N'VIEW', N'user_data'
END TRY
BEGIN CATCH
    IF (@@TRANCOUNT > 0) ROLLBACK
    PRINT ERROR_MESSAGE()
END CATCH
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_sessions'  AND sc.name = N'dbo'  AND si.name = N'browser_id' AND so.type in (N'U'))
   DROP INDEX [browser_id] ON [dbo].[user_sessions] 
GO
CREATE NONCLUSTERED INDEX [browser_id] ON [dbo].[user_sessions]
(
   [browser_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entity_modules'  AND sc.name = N'dbo'  AND si.name = N'comp_id' AND so.type in (N'U'))
   DROP INDEX [comp_id] ON [dbo].[entity_modules] 
GO
CREATE NONCLUSTERED INDEX [comp_id] ON [dbo].[entity_modules]
(
   [entity_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entities'  AND sc.name = N'dbo'  AND si.name = N'company_creator' AND so.type in (N'U'))
   DROP INDEX [company_creator] ON [dbo].[entities] 
GO
CREATE NONCLUSTERED INDEX [company_creator] ON [dbo].[entities]
(
   [creation_installer] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entities'  AND sc.name = N'dbo'  AND si.name = N'company_editor' AND so.type in (N'U'))
   DROP INDEX [company_editor] ON [dbo].[entities] 
GO
CREATE NONCLUSTERED INDEX [company_editor] ON [dbo].[entities]
(
   [edition_installer] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'app_elements'  AND sc.name = N'dbo'  AND si.name = N'element_method' AND so.type in (N'U'))
   DROP INDEX [element_method] ON [dbo].[app_elements] 
GO
CREATE NONCLUSTERED INDEX [element_method] ON [dbo].[app_elements]
(
   [module_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entity_options'  AND sc.name = N'dbo'  AND si.name = N'eoption_entity' AND so.type in (N'U'))
   DROP INDEX [eoption_entity] ON [dbo].[entity_options] 
GO
CREATE NONCLUSTERED INDEX [eoption_entity] ON [dbo].[entity_options]
(
   [entity_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entity_options'  AND sc.name = N'dbo'  AND si.name = N'eoption_option' AND so.type in (N'U'))
   DROP INDEX [eoption_option] ON [dbo].[entity_options] 
GO
CREATE NONCLUSTERED INDEX [eoption_option] ON [dbo].[entity_options]
(
   [option_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_logs'  AND sc.name = N'dbo'  AND si.name = N'log_action' AND so.type in (N'U'))
   DROP INDEX [log_action] ON [dbo].[user_logs] 
GO
CREATE NONCLUSTERED INDEX [log_action] ON [dbo].[user_logs]
(
   [action_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_logs'  AND sc.name = N'dbo'  AND si.name = N'log_element' AND so.type in (N'U'))
   DROP INDEX [log_element] ON [dbo].[user_logs] 
GO
CREATE NONCLUSTERED INDEX [log_element] ON [dbo].[user_logs]
(
   [element_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_logs'  AND sc.name = N'dbo'  AND si.name = N'log_user' AND so.type in (N'U'))
   DROP INDEX [log_user] ON [dbo].[user_logs] 
GO
CREATE NONCLUSTERED INDEX [log_user] ON [dbo].[user_logs]
(
   [user_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entity_methods'  AND sc.name = N'dbo'  AND si.name = N'method_id' AND so.type in (N'U'))
   DROP INDEX [method_id] ON [dbo].[entity_methods] 
GO
CREATE NONCLUSTERED INDEX [method_id] ON [dbo].[entity_methods]
(
   [method_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'app_methods'  AND sc.name = N'dbo'  AND si.name = N'module_id' AND so.type in (N'U'))
   DROP INDEX [module_id] ON [dbo].[app_methods] 
GO
CREATE NONCLUSTERED INDEX [module_id] ON [dbo].[app_methods]
(
   [module_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_modules'  AND sc.name = N'dbo'  AND si.name = N'module_id' AND so.type in (N'U'))
   DROP INDEX [module_id] ON [dbo].[user_modules] 
GO
CREATE NONCLUSTERED INDEX [module_id] ON [dbo].[user_modules]
(
   [module_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'entity_modules'  AND sc.name = N'dbo'  AND si.name = N'module_id' AND so.type in (N'U'))
   DROP INDEX [module_id] ON [dbo].[entity_modules] 
GO
CREATE NONCLUSTERED INDEX [module_id] ON [dbo].[entity_modules]
(
   [module_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'app_options'  AND sc.name = N'dbo'  AND si.name = N'option_module' AND so.type in (N'U'))
   DROP INDEX [option_module] ON [dbo].[app_options] 
GO
CREATE NONCLUSTERED INDEX [option_module] ON [dbo].[app_options]
(
   [module_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'users'  AND sc.name = N'dbo'  AND si.name = N'theme_id' AND so.type in (N'U'))
   DROP INDEX [theme_id] ON [dbo].[users] 
GO
CREATE NONCLUSTERED INDEX [theme_id] ON [dbo].[users]
(
   [theme_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_methods'  AND sc.name = N'dbo'  AND si.name = N'umethod_method' AND so.type in (N'U'))
   DROP INDEX [umethod_method] ON [dbo].[user_methods] 
GO
CREATE NONCLUSTERED INDEX [umethod_method] ON [dbo].[user_methods]
(
   [method_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_methods'  AND sc.name = N'dbo'  AND si.name = N'umethod_user' AND so.type in (N'U'))
   DROP INDEX [umethod_user] ON [dbo].[user_methods] 
GO
CREATE NONCLUSTERED INDEX [umethod_user] ON [dbo].[user_methods]
(
   [user_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_sessions'  AND sc.name = N'dbo'  AND si.name = N'user_id' AND so.type in (N'U'))
   DROP INDEX [user_id] ON [dbo].[user_sessions] 
GO
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[user_sessions]
(
   [user_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_modules'  AND sc.name = N'dbo'  AND si.name = N'user_id' AND so.type in (N'U'))
   DROP INDEX [user_id] ON [dbo].[user_modules] 
GO
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[user_modules]
(
   [user_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_recovery'  AND sc.name = N'dbo'  AND si.name = N'user_id' AND so.type in (N'U'))
   DROP INDEX [user_id] ON [dbo].[user_recovery] 
GO
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[user_recovery]
(
   [user_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (
       SELECT * FROM sys.objects  so JOIN sys.indexes si
       ON so.object_id = si.object_id
       JOIN sys.schemas sc
       ON so.schema_id = sc.schema_id
       WHERE so.name = N'user_sessions'  AND sc.name = N'dbo'  AND si.name = N'usession_branch' AND so.type in (N'U'))
   DROP INDEX [usession_branch] ON [dbo].[user_sessions] 
GO
CREATE NONCLUSTERED INDEX [usession_branch] ON [dbo].[user_sessions]
(
   [branch_id] ASC
)
WITH (SORT_IN_TEMPDB = OFF, DROP_EXISTING = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF) ON [PRIMARY] 
GO
GO

USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_elements$element_module'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[app_elements] DROP CONSTRAINT [app_elements$element_module]
 GO



ALTER TABLE [dbo].[app_elements]
 ADD CONSTRAINT [app_elements$element_module]
 FOREIGN KEY 
   ([module_id])
 REFERENCES 
   [blackphp].[dbo].[app_modules]     ([module_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_methods$method_module'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[app_methods] DROP CONSTRAINT [app_methods$method_module]
 GO



ALTER TABLE [dbo].[app_methods]
 ADD CONSTRAINT [app_methods$method_module]
 FOREIGN KEY 
   ([module_id])
 REFERENCES 
   [blackphp].[dbo].[app_modules]     ([module_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'app_options$option_module'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[app_options] DROP CONSTRAINT [app_options$option_module]
 GO



ALTER TABLE [dbo].[app_options]
 ADD CONSTRAINT [app_options$option_module]
 FOREIGN KEY 
   ([module_id])
 REFERENCES 
   [blackphp].[dbo].[app_modules]     ([module_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entities$company_creator'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entities] DROP CONSTRAINT [entities$company_creator]
 GO


/* 
*   SSMA error messages:
*   M2SS0041: ON DELETE CASCADE|SET NULL|SET DEFAULT action was changed to NO ACTION to avoid multiple paths in cascaded foreign keys.
*   M2SS0037: ON UPDATE CASCADE|SET NULL|SET DEFAULT action was changed to NO ACTION to avoid multiple paths in cascaded foreign keys.
*/


ALTER TABLE [dbo].[entities]
 ADD CONSTRAINT [entities$company_creator]
 FOREIGN KEY 
   ([creation_installer])
 REFERENCES 
   [blackphp].[dbo].[app_installers]     ([installer_id])
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entities$company_editor'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entities] DROP CONSTRAINT [entities$company_editor]
 GO



ALTER TABLE [dbo].[entities]
 ADD CONSTRAINT [entities$company_editor]
 FOREIGN KEY 
   ([edition_installer])
 REFERENCES 
   [blackphp].[dbo].[app_installers]     ([installer_id])
    ON DELETE SET NULL
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_methods$cmethod_company'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_methods] DROP CONSTRAINT [entity_methods$cmethod_company]
 GO



ALTER TABLE [dbo].[entity_methods]
 ADD CONSTRAINT [entity_methods$cmethod_company]
 FOREIGN KEY 
   ([entity_id])
 REFERENCES 
   [blackphp].[dbo].[entities]     ([entity_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_methods$cmethod_method'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_methods] DROP CONSTRAINT [entity_methods$cmethod_method]
 GO



ALTER TABLE [dbo].[entity_methods]
 ADD CONSTRAINT [entity_methods$cmethod_method]
 FOREIGN KEY 
   ([method_id])
 REFERENCES 
   [blackphp].[dbo].[app_methods]     ([method_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_modules$cmodule_company'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_modules] DROP CONSTRAINT [entity_modules$cmodule_company]
 GO



ALTER TABLE [dbo].[entity_modules]
 ADD CONSTRAINT [entity_modules$cmodule_company]
 FOREIGN KEY 
   ([entity_id])
 REFERENCES 
   [blackphp].[dbo].[entities]     ([entity_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_modules$cmodule_module'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_modules] DROP CONSTRAINT [entity_modules$cmodule_module]
 GO



ALTER TABLE [dbo].[entity_modules]
 ADD CONSTRAINT [entity_modules$cmodule_module]
 FOREIGN KEY 
   ([module_id])
 REFERENCES 
   [blackphp].[dbo].[app_modules]     ([module_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_options$eoption_entity'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_options] DROP CONSTRAINT [entity_options$eoption_entity]
 GO



ALTER TABLE [dbo].[entity_options]
 ADD CONSTRAINT [entity_options$eoption_entity]
 FOREIGN KEY 
   ([entity_id])
 REFERENCES 
   [blackphp].[dbo].[entities]     ([entity_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'entity_options$eoption_option'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[entity_options] DROP CONSTRAINT [entity_options$eoption_option]
 GO



ALTER TABLE [dbo].[entity_options]
 ADD CONSTRAINT [entity_options$eoption_option]
 FOREIGN KEY 
   ([option_id])
 REFERENCES 
   [blackphp].[dbo].[app_options]     ([option_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_logs$log_element'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_logs] DROP CONSTRAINT [user_logs$log_element]
 GO



ALTER TABLE [dbo].[user_logs]
 ADD CONSTRAINT [user_logs$log_element]
 FOREIGN KEY 
   ([element_id])
 REFERENCES 
   [blackphp].[dbo].[app_elements]     ([element_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_logs$log_user'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_logs] DROP CONSTRAINT [user_logs$log_user]
 GO



ALTER TABLE [dbo].[user_logs]
 ADD CONSTRAINT [user_logs$log_user]
 FOREIGN KEY 
   ([user_id])
 REFERENCES 
   [blackphp].[dbo].[users]     ([user_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_methods$umethod_method'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_methods] DROP CONSTRAINT [user_methods$umethod_method]
 GO



ALTER TABLE [dbo].[user_methods]
 ADD CONSTRAINT [user_methods$umethod_method]
 FOREIGN KEY 
   ([method_id])
 REFERENCES 
   [blackphp].[dbo].[app_methods]     ([method_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_methods$umethod_user'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_methods] DROP CONSTRAINT [user_methods$umethod_user]
 GO



ALTER TABLE [dbo].[user_methods]
 ADD CONSTRAINT [user_methods$umethod_user]
 FOREIGN KEY 
   ([user_id])
 REFERENCES 
   [blackphp].[dbo].[users]     ([user_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_modules$umodule_module'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_modules] DROP CONSTRAINT [user_modules$umodule_module]
 GO



ALTER TABLE [dbo].[user_modules]
 ADD CONSTRAINT [user_modules$umodule_module]
 FOREIGN KEY 
   ([module_id])
 REFERENCES 
   [blackphp].[dbo].[app_modules]     ([module_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_modules$umodule_user'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_modules] DROP CONSTRAINT [user_modules$umodule_user]
 GO



ALTER TABLE [dbo].[user_modules]
 ADD CONSTRAINT [user_modules$umodule_user]
 FOREIGN KEY 
   ([user_id])
 REFERENCES 
   [blackphp].[dbo].[users]     ([user_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_recovery$urecovery_user'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_recovery] DROP CONSTRAINT [user_recovery$urecovery_user]
 GO



ALTER TABLE [dbo].[user_recovery]
 ADD CONSTRAINT [user_recovery$urecovery_user]
 FOREIGN KEY 
   ([user_id])
 REFERENCES 
   [blackphp].[dbo].[users]     ([user_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_sessions$usession_browser'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_sessions] DROP CONSTRAINT [user_sessions$usession_browser]
 GO



ALTER TABLE [dbo].[user_sessions]
 ADD CONSTRAINT [user_sessions$usession_browser]
 FOREIGN KEY 
   ([browser_id])
 REFERENCES 
   [blackphp].[dbo].[browsers]     ([browser_id])
    ON DELETE SET NULL
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'user_sessions$usession_user'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[user_sessions] DROP CONSTRAINT [user_sessions$usession_user]
 GO



ALTER TABLE [dbo].[user_sessions]
 ADD CONSTRAINT [user_sessions$usession_user]
 FOREIGN KEY 
   ([user_id])
 REFERENCES 
   [blackphp].[dbo].[users]     ([user_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO


USE blackphp
GO
IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'users$user_company'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[users] DROP CONSTRAINT [users$user_company]
 GO



ALTER TABLE [dbo].[users]
 ADD CONSTRAINT [users$user_company]
 FOREIGN KEY 
   ([entity_id])
 REFERENCES 
   [blackphp].[dbo].[entities]     ([entity_id])
    ON DELETE CASCADE
    ON UPDATE CASCADE

GO

IF EXISTS (SELECT * FROM sys.objects so JOIN sys.schemas sc ON so.schema_id = sc.schema_id WHERE so.name = N'users$user_theme'  AND sc.name = N'dbo'  AND type in (N'F'))
ALTER TABLE [dbo].[users] DROP CONSTRAINT [users$user_theme]
 GO



ALTER TABLE [dbo].[users]
 ADD CONSTRAINT [users$user_theme]
 FOREIGN KEY 
   ([theme_id])
 REFERENCES 
   [blackphp].[dbo].[app_themes]     ([theme_id])
    ON DELETE SET NULL
    ON UPDATE CASCADE

GO


USE blackphp
GO
ALTER TABLE  [dbo].[app_elements]
 ADD DEFAULT 0 FOR [unique_element]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[app_installers]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[app_methods]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[app_modules]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[app_options]
 ADD DEFAULT 1 FOR [option_type]
GO

ALTER TABLE  [dbo].[app_options]
 ADD DEFAULT NULL FOR [module_id]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [admin_user]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [entity_subdomain]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT N'BlackPHP' FOR [app_name]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [default_locale]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [creation_installer]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [edition_installer]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [edition_user]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT NULL FOR [user_edition_time]
GO

ALTER TABLE  [dbo].[entities]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[entity_methods]
 ADD DEFAULT 1 FOR [method_order]
GO

ALTER TABLE  [dbo].[entity_methods]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[entity_modules]
 ADD DEFAULT 1 FOR [module_order]
GO

ALTER TABLE  [dbo].[entity_modules]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[entity_options]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[user_methods]
 ADD DEFAULT 255 FOR [access_type]
GO

ALTER TABLE  [dbo].[user_methods]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[user_modules]
 ADD DEFAULT NULL FOR [access_type]
GO

ALTER TABLE  [dbo].[user_modules]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[user_recovery]
 ADD DEFAULT 1 FOR [status]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[user_sessions]
 ADD DEFAULT NULL FOR [branch_id]
GO

ALTER TABLE  [dbo].[user_sessions]
 ADD DEFAULT NULL FOR [browser_id]
GO


USE blackphp
GO
ALTER TABLE  [dbo].[users]
 ADD DEFAULT NULL FOR [nickname]
GO

ALTER TABLE  [dbo].[users]
 ADD DEFAULT NULL FOR [email]
GO

ALTER TABLE  [dbo].[users]
 ADD DEFAULT 1 FOR [theme_id]
GO

ALTER TABLE  [dbo].[users]
 ADD DEFAULT NULL FOR [locale]
GO

ALTER TABLE  [dbo].[users]
 ADD DEFAULT 1 FOR [status]
GO

