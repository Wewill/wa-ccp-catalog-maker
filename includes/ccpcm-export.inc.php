<?php

require_once(dirname(__FILE__).'/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ccpcm_export extends ccpcm_object {
    private $fields = null;

	public function generate_data_by_filter($file) {
		$file_data = json_decode(file_get_contents($file), True);
		$type = $file_data['type'];
		$data = $this->ccpcm->data->get_data_as_array($type);
		$data = $this->reverse_array1($data);
		$data = $this->reverse_array2($data);
		return $data;
	}

	public function generate_file_by_filter($file) {
		die('ne fonctionne pas encore... patience...');
		$data = $this->generate_data_by_filter($file);
		$this->generate(False, $data);	
	}

	// tab excel to key => values
	public function reverse_array1($data) {
		if (!$data)
			return [];
		$headers = $data[0];
		$output = [];
		for($i=0; $i < count($data[1]); $i++) {
			if ($data[1][$i] == 'system')
				$headers[$i] = '_'.$headers[$i];
		}
		for($i=2; $i < count($data); $i++) {
			$output[] = array_combine($headers, $data[$i]);
		}
		return $output;
	}

	// key => values to tab excel
	public function reverse_array2($data) {
		if (!$data)
			return [];
		$output = [];
		$headers = [];
		$headers_idx = [];
		$idx = 0;
		foreach($data[0] as $key => $value) {
			if (substr($key, 0, 1) == '_')
				$headers[] = substr($key, 1);
			else
				$headers[] = $key;
			$headers_idx[$key] = $idx;
			$idx ++;
		}
		$output[] = $headers;
		for($i = 1; $i < count($data); $i++) {
			$d = [];
			foreach($data[$i] as $key => $value) {
				$d[$headers_idx[$key]] = $value;
			}
			$output[] = $d;
		}
		return $output;
	}

    public function generate($type = False, $data = False) {
//        die('export en cours de création... patience...');
	if (!$data)
		$data = $this->ccpcm->data->get_data_as_array($type);

        if ($data) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($data, null, 'A1');
            $styleFirstRow = [
                'font' => [
                    'bold' => true,
                ]
            ];
            $styleSecondRow = [
                'font' => [
                    'italic' => true,
                ]
            ];
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle('A1:' . $highestColumn . '1' )->applyFromArray($styleFirstRow);
            $sheet->getStyle('A2:' . $highestColumn . '2' )->applyFromArray($styleSecondRow);

            $fileName = sprintf('%s-%s.xlsx', $type, date('Y-m-d-his'));

            $writer = new Xlsx($spreadsheet);
            ob_get_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
            $writer->save('php://output');
            die();
        }
    }

    public function form_get_label($type, $field) {
        if (is_null($this->fields))
            $this->fields = $this->ccpcm->data->get_fields();
        switch($field) {
            case '_thumbnail_id':
                return "Photo principale";
            default:
		if (array_key_exists($field, $this->fields))
			return $this->fields[$field]['name'];
                break;
        }
	return $field;
    }
}
