<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'process' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'process_images' => ['nullable', 'array', 'max:10'],
            'process_images.*' => ['image', 'max:5120'],
            'process_image_keys' => ['nullable', 'string'],
            'result_images' => ['nullable', 'array', 'max:10'],
            'result_images.*' => ['image', 'max:5120'],
            'result_image_keys' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:20480'],
            'delete_file_ids' => ['nullable', 'array'],
            'delete_file_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'process_images.array' => '过程图片格式不正确。',
            'process_images.max' => '过程图片一次最多上传 10 张。',
            'process_images.*.image' => '过程图片必须是图片文件。',
            'process_images.*.max' => '过程图片不能超过 5MB。',
            'result_images.array' => '结果图片格式不正确。',
            'result_images.max' => '结果图片一次最多上传 10 张。',
            'result_images.*.image' => '结果图片必须是图片文件。',
            'result_images.*.max' => '结果图片不能超过 5MB。',
            'attachments.array' => '附件格式不正确。',
            'attachments.max' => '附件一次最多上传 10 个。',
            'attachments.*.file' => '附件必须是文件。',
            'attachments.*.max' => '附件不能超过 20MB。',
            'delete_file_ids.array' => '待删除文件格式不正确。',
        ];
    }
}
