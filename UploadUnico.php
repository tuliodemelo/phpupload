<?php

/** ****************************************************************************
 * UploadUnico - Classe para upload seguro de arquivos ao servidor
 * @author Tulio de Melo - tuliodemelo@gmail.com
 * @version 1.0.1
 * @since 04/04/2014
 **************************************************************************** */

class UploadUnico
{
	// Configurações do Upload
	private $tamanhoMaximo       	 // Tamanho máximo do arquivo em Kbytes
		  , $diretorioDestino 	  	 // Diretório de destino do arquivo enviado
		  , $estiloNomenclatura  	 // Estilo da nomenclatura do arquivo [original,numeros,letras,original_tratado]
		  , $arrMimesPermitidos 	 // Array de tipos mime permitidos
		  , $arrExtensoesPermitidas; // Array de extensões permitidas para Upload

	private $verificarTipoMime // Verificar tipo mime do arquivo
		  , $verificarExtensao; // Verificar extensão do arquivo
	
	// Informações do arquivo que está sendo enviado	  
	private $nomeOriginal
		  , $extensao
		  , $tipoMime
		  , $tamanho
		  , $temporario
		  , $nomeFinal
		  , $nomePersonalizado
		  , $prefixo
		  , $sufixo;
	
	// Informações de retorno pós upload
	private $codRetorno
	  	  , $msgRetorno
	  	  , $arrMsgErro;

	/**
	 * Método construtor.
	 */
	public function __construct() {
		
		$this->verificarTipoMime = true;
		$this->verificarExtensao = true;
		
		// Pega o total máximo permitido pelo server de acordo com diretiva PHP
		if(function_exists('ini_get')) {
			$this->tamanhoMaximo = ini_get('upload_max_filesize') * 1024; // Em Kb
		}
		else {
			$this->tamanhoMaximo = 8192; // 8 MB em Kb
		}
					
		$this->estiloNomenclatura = 'original';
	}
	
	// Getters e Setters
	public function setTamanhoMaximo( $tamanhoMaximo ) {
		$this->tamanhoMaximo = $tamanhoMaximo;
	}
	
	public function setDiretorioDestino( $diretorioDestino ) {
		if( substr($diretorioDestino, -1) != '/' ) {
			$diretorioDestino = $diretorioDestino.'/';
		}
			
		$this->diretorioDestino = $diretorioDestino;
	}
	
	public function setEstiloNomenclatura( $estiloNomenclatura ) {
		$this->estiloNomenclatura = $estiloNomenclatura;
	}

	public function setArrMimesPermitidos( $arrMimesPermitidos ) {
		$this->arrMimesPermitidos = $arrMimesPermitidos;
	}	
	
	public function setArrExtensoesPermitidas( $arrExtensoesPermitidas ) {
		$this->arrExtensoesPermitidas = $arrExtensoesPermitidas;
	}
	
	public function setVerificarTipoMime($verificarTipoMime) {
		$this->verificarTipoMime = $verificarTipoMime;
	}
	
	public function setVerificarExtensao($verificarExtensao) {
		$this->verificarExtensao = $verificarExtensao;
	}
	
	public function getNomeFinal() {
		return $this->nomeFinal;
	}
	
	public function getCodRetorno() {
		return $this->codRetorno;
	}
	
	public function getMsgRetorno() {
		return $this->msgRetorno;
	}
	
	public function setNomePersonalizado($nomePersonalizado) {
		$this->nomePersonalizado = $nomePersonalizado;
	}
	
	public function setPrefixo($prefixo) {
		$this->prefixo = $prefixo;
	}
	
	public function setSufixo($sufixo) {
		$this->sufixo = $sufixo;
	}
	
	/**
	 * Realiza as verificações dos padrões de arquivo permitidos.
	 * @return boolean
	 */
	private function verificaArquivo() {

		$valido = true;
		
		// ? Verifica se o tipo mime do arquivo esté nos mimes permitidos
		if($this->verificarTipoMime) {
			if(count($this->arrMimesPermitidos) == 0) {
				$msg = "Tipos mime permitidos não informados.";
				
				$this->arrMsgErro[] = $msg;
				$valido = false;
			}
			elseif( !in_array( $this->tipoMime, $this->arrMimesPermitidos ) ) {
	            $msg = "Tipo mime ({$this->tipoMime}) de arquivo não permitido.";
	            
	            $this->arrMsgErro[] = $msg;
				$valido = false;		
			}
		}
		
		// ? Verifica se esta extensão está entre as permitidas
		if($this->verificarExtensao) {
			if(count($this->arrExtensoesPermitidas) == 0) {
				$msg = "Extensões permitidas não informadas.";
				
				$this->arrMsgErro[] = $msg;
				$valido = false;
			}
			elseif( !in_array( $this->extensao, $this->arrExtensoesPermitidas ) ) {
	            $msg = "Extensão de arquivo ({$this->extensao}) não permitida.";
	            
	            $this->arrMsgErro[] = $msg;
				$valido = false;
			}
		}
		
		// Verifica se o tamanho do arquivo está entre o permitido
		// Transforma em bytes para comparar certinho
		if( $this->tamanho > ($this->tamanhoMaximo*1024) ) {
            $msg = "Arquivo com tamanho superior ao permitido";
            
			$this->arrMsgErro[] = $msg;
			$valido = false;
		}
		
		if( !$valido ) 	{
			$this->codRetorno = 0;
      		$this->msgRetorno = 'Arquivo não passou na verificação de segurança';
		}
		
		return $valido;
	}
	
	/**
	 * Recupera a extensão do arquivo.
	 * @param string $nomeArquivo
	 * @return string
	 */
	private function extensaoArquivo($nomeArquivo) {
		return strtolower( strrchr( $nomeArquivo, '.') );
	}
	
	/**
	 * Configura a nomenclatura final do arquivo.
	 * @return string
	 */
	private function configuraArquivo() {
		
		$nomeArquivoConfigurado = "";
		
		if($this->prefixo != "") {
			$nomeArquivoConfigurado = $this->prefixo.$nomeArquivoConfigurado;
		}
		
		switch( $this->estiloNomenclatura ) {
			
			case 'numeros':
				$nomeArquivoConfigurado .= rand(1,9).rand(1,9).rand(1,9).rand(1,9).time().$this->extensao;
			break;
			
			case 'letras':
				$arr_letras = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
								   "l", "m", "n", "o", "p", "q", "r", "s", "t" , "u", "v",
								   "w", "x", "y", "z"
								   );
	
				for( $i=0; $i<14; $i++ ) {
					$indice = rand( 0, count($arr_letras) );
					$nomeArquivoConfigurado .= $arr_letras[$indice];
				}
				$nomeArquivoConfigurado .= $this->extensao;				
			break;
			
			case 'original_tratado':
				$nomeSemExtensao = str_replace($this->extensao, "", $this->nomeOriginal);
				$nomeArquivoConfigurado .= $this->sanitizeString($nomeSemExtensao);
				$nomeArquivoConfigurado .= $this->extensao;
			break;
			
			case 'personalizado':
				$nomeArquivoConfigurado .= $this->nomePersonalizado;
				if(!strrchr( $nomeArquivoConfigurado, '.') ) {
					$nomeArquivoConfigurado .= $this->extensao;
				}
			break;
			
			default:	
				$nomeArquivoConfigurado .= $this->nomeOriginal;
			break;			
		}	
		
		if($this->sufixo != "") {
			$nomeSemExtensao = str_replace($this->extensao, "", $nomeArquivoConfigurado);
			$nomeArquivoConfigurado = $nomeSemExtensao.$this->sufixo.$this->extensao;
		}
		
		return $nomeArquivoConfigurado; 
	}	
	
	/**
	 * Remove caracteres especiais do nome do arquivo.
	 * @param string $str
	 * @return string
	 */
	private function sanitizeString($str) {
		$str = preg_replace('/[áàãâä]/ui', 'a', $str);
		$str = preg_replace('/[éèêë]/ui', 'e', $str);
		$str = preg_replace('/[íìîï]/ui', 'i', $str);
		$str = preg_replace('/[óòõôö]/ui', 'o', $str);
		$str = preg_replace('/[úùûü]/ui', 'u', $str);
		$str = preg_replace('/[ç]/ui', 'c', $str);
		// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
		$str = preg_replace('/[^a-z0-9]/i', '_', $str);
		$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
		return $str;
	}
	
	/**
	 * Realiza o upload do arquivo e retorna informações.
	 * @param string $nomeCampo
	 */
	public function upload( $nomeCampo ) {
		if ( !empty( $_FILES[$nomeCampo]["name"] ) ) {
				
			// -> Nome original do arquivo
			$this->nomeOriginal = $_FILES[$nomeCampo]["name"];
			  		
			// -> Extrai a extensão do arquivo para primeira verificação
		  	$this->extensao = $this->extensaoArquivo($_FILES[$nomeCampo]["name"]);
		  	
		  	// Tipo mime do arquivo
			$this->tipoMime = $_FILES[$nomeCampo]["type"];	
			
			// Tamanho do arquivo em bytes
			$this->tamanho = $_FILES[$nomeCampo]["size"];
			
			// Nome do temporário
			$this->temporario = $_FILES[$nomeCampo]["tmp_name"];
			
			// Verifica se o arquivo pode ser enviado de acordo com as restrições
			$liberado = $this->verificaArquivo();
			
			if( $liberado ) {
				
				// -> Envia o arquivo
				$this->nomeFinal = $this->configuraArquivo();
     
      			if( !move_uploaded_file( $_FILES[$nomeCampo]["tmp_name"], $this->diretorioDestino.$this->nomeFinal ) ) {
      				$this->codRetorno = 0;
      				$this->msgRetorno = 'O upload não foi executado corretamente';
      			}
      			else {
      				$this->codRetorno = 1;
      				$this->msgRetorno = 'Sucesso!';
      			}
			}			  	
		}
	}
}

?>