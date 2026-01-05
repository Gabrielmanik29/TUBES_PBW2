


namespace Database\Factories;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition()
    {
        $tanggalPinjam = $this->faker->dateTimeBetween('-1 month', 'now');
        $tanggalKembali = Carbon::parse($tanggalPinjam)->addDays(rand(1, 7));
        
        $status = $this->faker->randomElement(['diajukan', 'disetujui', 'ditolak', 'dikembalikan']);
        
        $denda = 0;
        $dendaDibayar = false;
        
        if ($status === 'dikembalikan') {
            $tanggalPengembalian = $this->faker->dateTimeBetween($tanggalPinjam, $tanggalKembali);
            
            // 30% kemungkinan terlambat
            if ($this->faker->boolean(30)) {
                $tanggalPengembalian = Carbon::parse($tanggalKembali)->addDays(rand(1, 5));
                $hariTerlambat = Carbon::parse($tanggalPengembalian)->diffInDays($tanggalKembali);
                $denda = $hariTerlambat * 10000;
                $dendaDibayar = $this->faker->boolean(70); // 70% sudah bayar
            }
        }

        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'tanggal_pengembalian_aktual' => $status === 'dikembalikan' 
                ? ($tanggalPengembalian ?? null) 
                : null,
            'status' => $status,
            'denda' => $denda,
            'denda_dibayar' => $dendaDibayar,
        ];
    }
}
