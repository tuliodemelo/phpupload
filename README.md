# phpupload
PHPUpload é um conjunto de classes escritas em PHP5 que simplificam o processo de upload de arquivos em PHP.

As classes disponibilizadas por este projeto são:

 - UploadUnico.php - Permite o upload de um unico arquivo ao servidor
 - UploadMultiplo.php - Permite o upload de múltiplos arquivos ao servidor

# How-to UploadUnico.php

```php

// Inclua a classe e crie um objeto da mesma (instância)
include("UploadUnico.php");
$uploadu = new UploadUnico();

// Configure o upload
$uploadu->setTamanhoMaximo(8192); // Não obrigatório - Default Sistema 
$uploadu->setDiretorioDestino('./fotos/'); // Informe o path adequado
$uploadu->setEstiloNomenclatura('numeros'); // Não obrigatório - Default original
$uploadu->setArrMimesPermitidos( array("image/pjpeg", "image/jpeg", "image/png") );
$uploadu->setArrExtensoesPermitidas( array(".jpeg", ".jpg",".png") );
	
// Verificar tipo mime e extensão - Não obrigatório - Default true
$uploadu->setVerificarTipoMime(false);
$uploadu->setVerificarExtenso(false);
	
// Realiza o upload
$uploadu->upload('nomedomeucampoinputfile');	
	
if( $uploadu->getCodRetorno() == 1 ) {

	// Para recuperar o nome do arquivo que foi enviado utilize
	$arquivoEnviado = $uploadu->getNomeFinal();
}

```

#### Configurações adicionais

A UploadUnico permite ao desenvolvedor configurar o tipo de saída do nome do arquivo.

Veja os exemplos:

> Configurando o tipo de nomenclatura desejado para arquivo enviado

```php
$uploadu->setEstiloNomenclatura('numeros'); 
$uploadu->setEstiloNomenclatura('letras'); 
$uploadu->setEstiloNomenclatura('original_tratado'); 
$uploadu->setEstiloNomenclatura('personalizado'); 
```

> Definindo um nome personalizado para o arquivo de saída

```php
$uploadu->setEstiloNomenclatura('personalizado'); // Opcional - Default original
$uploadu->setNomePersonalizado($username."_".mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9).mt_rand(0, 9));
// A saída será por exemplo: tuliodemelo5219
```

> Adicionando um prefixo e/ou sufixo ao nome final do arquivo

```php
$uploadu->setEstiloNomenclatura('numeros');
$uploadu->setPrefixo('fotoperfil-'); 
$uploadu->setSufixo('-'.date("Ymd")); 
// A saída será por exemplo: fotoperfil-69231245-20150522
```



