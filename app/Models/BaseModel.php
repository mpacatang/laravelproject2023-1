<?php

namespace App\Models;

use App\Helpers\Util\ImageUtil;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed $id
 * @method static $this where(string|Closure $string, $comp = '', $shop_id = '')
 * @method static $this[]|Collection all()
 * @method static $this whereIn(string $string, mixed $lat_1)
 * @method static $this create(array $data)
 * @method static $this find(int $id)
 * @method static $this findOrFail($id)
 * @method static when(bool $param, Closure $param1)
 * @method $this[] get()
 * @method static $this first()
 * @method static count()
 * @method static $this whereDate(string $string, mixed $dt)
 * @method static $this whereMonth(string $string, $format)
 * @method static $this sum(string $string)
 * @method static $this whereHas(string $string, Closure $param)
 * @method $this firstOrFail()
 * @method $this orWhere(Closure|string $param, ?string $operator = "=", ?mixed $s = '')
 * @method $this orderBy(string $string, string $string1)
 * @method bool exists()
 * @method static $this findMany(mixed $seller_ids)
 * @method $this whereBetween(string $string, array $array)
 * @method pluck(string $string)
 * @method $this orderByDesc(string $string)
 * @method $this whereYear(string $string, $format)
 * @method static $this|Builder with(string[]|mixed|string ...$relations)
 * @method static $this|Builder withAll(string|string[] $relations)
 */
abstract class BaseModel extends Model
{

    protected string $modelImageKey = 'image';
    protected string $imageBaseLocation = 'images/';

    protected array|string $manuallyRelations = [];

    //----- Model Load ------//
    public static function withLoaded(): Builder|self
    {
        return static::with(self::$manuallyRelations);
    }

    public function loadRelations(): self
    {
        return $this->load($this->manuallyRelations);
    }


    //---- Image -------//
    public function saveImageFromRequest(
        Request $request,
        $imageBaseLocation = null,
        $modelKey = null,
        $requestImageKey = null
    ): bool {
        $imageBaseLocation ??= $this->imageBaseLocation;
        $modelKey ??= $this->modelImageKey;
        $requestImageKey ??= 'image';

        $data = ImageUtil::getImageOrNull($request->get($requestImageKey));

        if ($data) {
            $old_url = $this[$modelKey];
            try {
                $url = $imageBaseLocation . Str::random();
                if (Storage::disk('public')->put($url, $data)) {
                    $this[$modelKey] = $url;
                    if ($old_url != null) {
                        Storage::disk('public')->delete($old_url);
                    }
                }
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }


    public function removeImage($modelKey = null): bool
    {
        $modelKey ??= $this->modelImageKey;

        if (!$this[$modelKey] || Storage::disk('public')->delete($this[$modelKey])) {
            $this[$modelKey] = null;
        }
        return true;
    }


}
