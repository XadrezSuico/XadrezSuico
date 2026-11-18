<?php
namespace Tests\Unit;

use App\Helper\SwissManagerPairingsCsv;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SwissManagerPairingsCsvTest extends TestCase
{
    public function test_bom_spaces_quotes_crlf_and_result_alias_are_supported()
    {
        $rows = SwissManagerPairingsCsv::parse("\xEF\xBB\xBF Rodada ;Mesa;ID-B;ID-N;NoB;NoN;ResB;ResN\r\n1;2;100;200;1;2;\"0,5\";\"0,5\"\r\n\r\n");
        $this->assertCount(2, $rows);
        $this->assertSame('Rodada', $rows[0][0]);
        $this->assertSame('ResM', $rows[0][7]);
        $this->assertSame('0,5', $rows[1][6]);
    }
    /** @dataProvider invalidFiles */
    public function test_invalid_files_are_rejected_before_import($csv)
    {
        $this->expectException(ValidationException::class);
        SwissManagerPairingsCsv::parse($csv);
    }
    public function invalidFiles()
    {
        $header = "Rodada;Mesa;ID-B;ID-N;NoB;NoN;ResB;ResM\n";
        return [[''], ['Nome;Rating\nJogador;1800'], [$header], [$header.'1;2;3'], [$header.'0;1;1;2;1;2;1;0']];
    }
}
