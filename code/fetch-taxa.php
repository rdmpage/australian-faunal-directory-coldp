<?php

$stack = array();


//----------------------------------------------------------------------------------------
function fetch_info($key)
{
	$children = array();
	
	$url = "https://biodiversity.org.au/afd/taxa/$key/checklist-subtaxa.json";

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	if ($data != '')
	{
		$obj = json_decode($data);
		
		foreach ($obj as $item)
		{
			$has_children = false;
			if (isset($item->children))
			{
				if (count($item->children)) 
				{
					$has_children = true;
				}
			}
			
			if ($has_children)
			{
				foreach ($item->children as $child)
				{
					$children[] = $child->metadata->nameKey;
				}
			}
			else
			{
				$children[] = $item->metadata->nameKey;
			}
			
		}
	}
	
	//print_r($children);
	
	return $children;
			
}	

//----------------------------------------------------------------------------------------
// https://stackoverflow.com/a/38130611
function fetch_csv($key, $basedir, $force = false)
{
	global $stack;
	
	$filename = $basedir . '/' . $key . '.csv';
	
	if (file_exists($filename) && !$force)
	{
		echo "Have already\n";
		$ok = true;
	}
	else
	{
		$ok = false;

		$url = "https://biodiversity.org.au/afd/taxa/$key/names/csv/$key.csv";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/csv",
			"Accept-Charset: UTF-8"
			));	

		$data = curl_exec($ch);
		$info = curl_getinfo($ch); 
		curl_close($ch);
	
		if (preg_match('/DOCTYPE html PUBLIC/', $data))
		{
			echo "$key has too many records\n";
		
			$children = fetch_info($key);
			foreach ($children as $child)
			{
				$stack[] = $child;
			}
		}
		else
		{
			echo "$key.csv\n";
			$ok = true;
			file_put_contents($filename, $data);
		}
	}
	
	return $ok;
}



$stack = array(
// ANIMALIA
'ACANTHOCEPHALA',
'PORIFERA',
'TARDIGRADA',
'CNIDARIA',
'CTENOPHORA',
'DICYEMIDA',
'PLATYHELMINTHES',
'XENACOELOMORPHA',
'NEMERTEA',
'GASTROTRICHA',
'ROTIFERA',
'CHAETOGNATHA',
'KINORHYNCHA',
'GNATHOSTOMULIDA',
'LORICIFERA',
'NEMATODA',
'NEMATOMORPHA',
'BRYOZOA',
'BRACHIOPODA',
'MOLLUSCA',
'PHORONIDA',
'PRIAPULIDA',
'SIPUNCULA',
'ECHIURA',
'KAMPTOZOA',
'ANNELIDA',
'ONYCHOPHORA',
'ARTHROPODA',
'ECHINODERMATA',
'HEMICHORDATA',
'CHORDATA',
// PROTISTA
'OPISTHOKONTA',
'HAPLOSPORIDIA',
'SARCOMASTIGOPHORA',
'PARAMYXEA',
'DINOFLAGELLATA',
'EUGLENOZOA',
'PARABASALIA',
'PREAXOSTYLA',
'FORNICATA',
'MICROSPORIDIA',
'APICOMPLEXA',
'AMOEBOZOA',
'RETARIA',
'HETEROLOBOSEA',
'CERCOZOA',
'CILIOPHORA'
);

//$stack = array('STRATIOMYIDAE');

$stack = array(
'HETEROBRANCHIA',
'PLATYHELMINTHES',
'24216378-08c4-457c-a744-ef754737d285',
'Entiminae',
'Boletobiinae',
'CERAMBYCINAE',
'5a351525-ee15-408d-82d4-669ecb361e1c',
'PYRALOIDEA',
'Ennominae',
'STENOMATINAE',
'Sterrhinae',
'LAMIINAE',
'Erebinae',
'Buprestinae',
'FORMICIDAE',
'Chrysomelinae',
'SENTICAUDATA',
'Tenebrioninae',
'Rivulinae',
'Geometrinae',
'Molytinae',
'POLYCHAETA;ANNELIDA',
'Eulepidotinae',
'SPILOMELINAE;CRAMBIDAE',
'ASELLOTA',
'ANIMALIA',
'PENTATOMOIDEA',
'ACANTHUROIDEI',
'AVES',
'ACATHININA',
'PLEOCYEMATA',
'FULGOROIDEA',
'COCCOIDEA',
'PHYLLODOCIDA',
'CAELIFERA',
'ORTHORRHAPHA',
'SERPENTES',
'TEREBELLIDA',
'PERCOIDEI',
'EUTHYNEURA',
'HEXACORALLIA',
'MYGALOMORPHAE',
'SPIRURINA',
'IMPARIDENTIA',
'FLUSTRINA',
'TENEBRIONOIDEA',
'TEREBRANTIA',
'AETEINA',
'AETHROIDEA',
'LACERTILIA',
'CHALCIDOIDEA',
'AGLOSSATA',
'HETEROSCLEROMORPHA',
'ARANEOMORPHAE',
'VERTEBRATA',
'OPOMYZOIDEA',
'ELOPOMORPHA',
'OCTOCORALLIA',
'STERNORRHYNCHA',
'ENDEOSTIGMATA',
'XIPHIDIATA',
'ALUCITOIDEA',
'COREOIDEA',
'STROMATEOIDEI',
'ARACHNIDA',
'TRACHINOIDEI',
'PROTISTA',
'GNATHOSTOMATA',
'PYLOPULMONATA',
'AMPHILOCHIDEA',
'EUNICIDA',
'EUCARIDA',
'PHREATOICIDEA',
'DRACUNCULINA',
'LEBERTIOIDEA',
'HETERODONTA',
'VALVIFERA',
'TRIGYNASPIDA',
'BOMBYCOIDEA',
'CIMICOIDEA',
'MUSCOIDEA',
'CURCULIONOIDEA',
'CYMOTHOIDA',
'PROSTIGMATA',
'APIFORMES',
'ASILOIDEA',
'MOLLUSCA',
'APOCREADIATA',
'TUNICATA',
'ARADOIDEA',
'CAENOGASTROPODA',
'PTERIOMORPHIA',
'SCOLECIDA',
'TENTHREDINOIDEA',
'BRANCHIURA',
'YPONOMEUTOIDEA',
'HELICINA',
'ARRENUROIDEA',
'NOTOTHENIOIDEI',
'LYGAEOIDEA',
'ASCARIDINA',
'CYCLORRHAPHA',
'THECOSTRACA',
'TREMATODA',
'HYGROBATOIDEA',
'ECHINODERMATA',
'ORIBATIDA',
'TABANOIDEA',
'ACANTHOPTERYGII',
'TROMBICULOIDEA',
'EVANIOIDEA',
'CARNOIDEA',
'CYNIPOIDEA',
'RHAGIONOIDEA',
'PROCTOTRUPOIDEA',
'HEMIURATA',
'Harpalinae',
'Curculioninae',
'Doryctinae',
'Braconinae',
'STATHMOPODINAE',
'Epidesmiinae',
'Cheloninae',
'Pseudomorphinae',
'Alysiinae',
'Opiinae',
'Rutelinae',
'Herminiinae',
'Aphodiinae',
'Aleocharinae',
'Dorylinae',
'Hypeninae',
'Megophthalminae',
'Aganainae',
'Agathidinae',
'Eumolpinae',
'Oenochrominae_s.l.',
'BUPRESTIDAE',
'Cossoninae',
'Rogadinae',
'Alleculinae',
'Spilopyrinae',
'Galerucinae',
'ACENTROPINAE',
'Bruchinae;CHRYSOMELIDAE',
'Amblyoponinae',
'HYLAEINAE',
'Cyclominae',
'Rhyssalinae',
'CRAMBINAE',
'PRIONINAE',
'AUTOSTICHINAE',
'ODONTIINAE',
'Proteininae',
'Scoliopteryginae',
'Dolichoderinae',
'Oenochrominae_s._str.',
'Microgastrinae',
'Aphidiinae',
'Sigalphinae',
'Broscinae',
'Cassidinae',
'INSECTA',
'XYLORYCTINAE',
'Arctiinae',
'Lymantriinae',
'Euphorinae',
'SPONDYLIDINAE',
'Cardiochilinae',
'Larentiinae',
'Deltocephalinae',
'Myrmicinae',
'Lysiterminae',
'Leptotyphlinae',
'CICADELLIDAE',
'Eurymelinae',
'Euaesthetinae',
'Helconinae',
'Hormiinae',
'MUSOTIMINAE',
'Melolonthinae',
'GELECHIOIDEA',
'CHRYSIDOIDEA',
'NEMATOCERA',
'BIVESICULATA',
'BLABEROIDEA',
'BLENNIOIDEI',
'SCARABAEOIDEA',
'CONOIDEA',
'POLYPHAGA',
'SESIOIDEA',
'ELATEROIDEA',
'EMPIDOIDEA',
'CRUSTACEA',
'CEPHALOCHORDATA',
'EPHYDROIDEA',
'INTEGRIPALPIA',
'NEOGASTROPODA',
'TINEOIDEA',
'BUCEPHALATA',
'Brachycerinae',
'OECOPHORINAE',
'Conoderinae',
'Iassinae',
'Pselaphinae',
'Trechinae',
'Betylobraconinae',
'Blacinae',
'BLATTOIDEA',
'Oxytelinae',
'EURYGLOSSINAE',
'COPEPODA',
'CALAPPOIDEA',
'PORIFERA',
'CALLIONYMOIDEI',
'OESTROIDEA',
'CALYPTOSTOMATOIDEA',
'LITTORINIMORPHA',
'OCYPODOIDEA',
'CANCROIDEA',
'EUTELEOSTEI',
'PORTUNOIDEA',
'GEKKOTA',
'CARPILIOIDEA',
'COPROMORPHOIDEA',
'CASTNIOIDEA',
'PENTASTOMIDA',
'CERAPHRONOIDEA',
'CULICOMORPHA',
'CERCOPOIDEA',
'SORBEOCONCHA',
'SPIONIDA',
'LAUXANIOIDEA',
'GONEPLACOIDEA',
'MYRIAPODA',
'PISCES',
'VERONGIMORPHA',
'PSEUDOZIOIDEA',
'CHROMADORIA;CHROMADOREA',
'SPHAEROCEROIDEA',
'CHYZERIOIDEA',
'CICADOIDEA',
'LABROIDEI',
'CLUPEOMORPHA',
'VETIGASTROPODA',
'GLOSSATA',
'SCIOMYZOIDEA',
'HEXAPODA',
'COLOMASTIGIDEA',
'INTRAMACRONUCLEATA',
'SCHIZOPHORA',
'PASSERI',
'CORYDIOIDEA',
'CORYSTOIDEA',
'COSSOIDEA',
'ARCHOSAURIA',
'PALICOIDEA',
'CRYPTOCHIROIDEA',
'GYMNOLAEMATA',
'CNIDARIA',
'PERACARIDA',
'ARCHOSTEMATA',
'ZYGAENOIDEA',
'TIPULOMORPHA',
'NERIOIDEA',
'COLLETINAE',
'Calpinae',
'Migadopinae',
'Formicinae',
'Idiocerinae',
'Carabinae',
'Scaritinae',
'Sagrinae;CHRYSOMELIDAE',
'SCHOENOBIINAE',
'Pambolinae',
'Ulopinae',
'Cetoniinae',
'Cicadellinae',
'Cicindelinae',
'Cryptocephalinae',
'Stenochiinae;TENEBRIONOID_BRANCH',
'Exothecinae',
'Scarabaeinae',
'Omaliinae',
'Scolytinae',
'Criocerinae',
'Anobinae',
'GLAPHYRIINAE',
'Diaperinae',
'Cryptorhynchinae',
'7131a372-0d05-4adc-8f46-cd0447ce4c01',
'Dynastinae',
'DAIROIDEA',
'KERATOSA',
'Pterygotes',
'RHABDITICA',
'DIGENEA',
'HETEROPTERA;HEMIPTERA',
'TRAPEZIOIDEA',
'DORIPPOIDEA',
'NEMATODA',
'DREPANOIDEA',
'CARABOIDEA',
'Orgilinae',
'TANTULOCARIDA',
'Desmobathrinae',
'Typhlocybinae',
'Staphylininae',
'Dirrhopinae',
'Proceratiinae',
'MIDILINAE',
'Donaciinae',
'Dryophthorinae',
'ECHINOSTOMATA',
'GOBIOIDEI',
'EUPULMONATA',
'GASTROPODA',
'EPERMENIOIDEA',
'MAJOIDEA',
'MONOGYNASPIDA',
'CHIONEINAE',
'ERIPHIOIDEA',
'ERYTHRAEOIDEA',
'CRASSICLITELLATA',
'TECTIPLEURA',
'NOCTUOIDEA',
'EYLAIOIDEA',
'Ecnomiinae',
'Osoriinae',
'Pangraptinae',
'PARANDRINAE',
'Euacanthellinae',
'PILUMNOIDEA',
'LEPTONIDINA',
'GASTROCHAENIDINA',
'GRAPSOIDEA',
'GECARCINUCOIDEA',
'SCOMBROIDEI',
'GNATHOSTOMATINA',
'GOBIESOCOIDEI',
'ENSIFERA',
'Glypholomatinae',
'Gnamptodontinae',
'Ectatomminae',
'LIMNORIIDEA',
'MYODOCOPA',
'HAPLOSPLANCHNATA',
'DIPLOTESTICULATA',
'IDIOSTOLOIDEA',
'PAPILIONOIDEA',
'POSTCILIODESMATOPHORA',
'HEXAPODOIDEA',
'HIPPOBOSCOIDEA',
'CLITELLATA',
'Acari',
'HYBLAEOIDEA',
'HYDRACHNOIDEA',
'STAPHYLINOIDEA',
'HYDRYPHANTOIDEA',
'ANNULIPALPIA',
'HYMENOSOMATOIDEA',
'Macropsinae',
'Histeromerinae',
'Homolobinae',
'Hypocalinae',
'CARDIIDINA',
'ICHNEUMONOIDEA',
'IMMOIDEA',
'INGOLFIELLOIDEA',
'LEUCOSIOIDEA',
'KURTOIDEI',
'TENEBRIONIDAE',
'PYRRHOCOROIDEA',
'LEPOCREADIATA',
'PHYLLOCARIDA',
'ONISCIDEA',
'LIMNOPHILINAE',
'LIMONIINAE',
'TEPHRITOIDEA',
'SPHAEROLICHIDA',
'LUMBRICULATA',
'HYGROPHILA',
'Paederinae',
'Ledrinae;CICADELLIDAE',
'Leptanillinae',
'Hypenodinae',
'Toxocampinae',
'PTEROPHOROIDEA',
'CHRYSOMELOIDEA',
'MEGALYROIDEA',
'MEMBRACOIDEA',
'MEMBRANIPORINA',
'ZEUGLOPTERA',
'PRONOCEPHALATA',
'TROMBIDIOIDEA',
'MIROIDEA',
'PLECTIA',
'MONORCHIATA',
'VESPOIDEA',
'MYMAROMMATOIDEA',
'MYSTACOCARIDA',
'ANNELIDA',
'Macrocentrinae',
'Coelidiinae',
'Scydmaeninae',
'Maxfischeriinae',
'Megalopsidiinae',
'Mesostoinae',
'Meteorideinae',
'Miracinae',
'Psydrinae',
'Tachyporinae',
'Myrmeciinae',
'NERITIMORPHA',
'PLATYGASTROIDEA',
'PROTOBRANCHIA',
'Rhysipolinae',
'Evacanthinae',
'OPISTHORCHIATA',
'ORUSSOIDEA',
'ANABANTOIDEI',
'OSTEOGLOSSOMORPHA',
'SABELLIDA',
'SACOGLOSSA',
'OXYURINA',
'Hydrangeocolinae',
'Paussinae',
'XANTHOIDEA',
'ACOCHLIDIIMORPHA',
'PARTHENOPOIDEA',
'COLEORRHYNCHA',
'DENDROBRANCHIATA',
'CERIANTHARIA',
'TUBULIFERA',
'PHOLIDICHTHYOIDEI',
'BRYOZOA',
'HYPERIIDEA',
'PINNOTHEROIDEA',
'TYRANNI',
'PODOCOPA',
'BRACHYURA',
'PSEUDOCARCINOIDEA',
'TANYPEZOIDEA',
'CHELICERATA',
'Ichneutinae',
'Aclopinae',
'Brachininae',
'PSOCODEA',
'Platypodinae',
'Ponerinae',
'Piestinae',
'Phloeocharinae',
'PYRAUSTINAE',
'REDUVIOIDEA',
'SCOMBROLABRACOIDEI',
'SCRUPARIINA',
'SEJIDA',
'SPHAEROMATIDEA',
'THYRIDOIDEA',
'SIPHONARIMORPHA',
'SIRICOIDEA',
'MYXOPHAGA',
'APOIDEA',
'SPHINGOIDEA',
'STEPHANOIDEA',
'HOPLOCARIDA',
'EUMALACOSTRACA',
'Scaphidiinae',
'Brachistinae',
'SCOPARIINAE',
'Tartessinae',
'Steninae',
'TAINISOPIDEA',
'TENDRINA',
'REPTILIA',
'THALAMOPORELLINA',
'THAUMASTOCOROIDEA',
'TINGOIDEA',
'TORTRICOIDEA',
'TRANSVERSOTREMATA',
'TRICHOPELTARIOIDEA',
'TRIGONALOIDEA',
'PALAEOHETERODONTA',
'TUBIFICATA',
'Pseudomyrmecinae',
'BRACONIDAE',
'Trichophyinae',
'URANIOIDEA',
'XIPHYDRIOIDEA',
'STRATIOMYOIDEA',
'XYLOPHAGOMORPHA',
'Xestocephalinae',
'ZOARCOIDEI',

);

$stack = array(
'BRACONIDAE',
);

$basedir = dirname(dirname(__FILE__)) . '/taxa';


while (count($stack) > 0)
{
	$node = array_pop($stack);

	echo "Fetching node $node...\n";
	
	fetch_csv($node, $basedir);
}

?>