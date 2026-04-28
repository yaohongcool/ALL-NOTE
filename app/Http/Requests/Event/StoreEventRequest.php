<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', Rule::in(Event::STATUSES)],
            'subject' => ['nullable', 'string', 'max:255'],
            'occurred_on' => ['nullable', 'date'],
            'visibility' => ['required', 'string', Rule::in(Event::VISIBILITIES)],
            'process' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'event_tag_ids' => ['nullable', 'array'],
            'event_tag_ids.*' => ['integer'],
            'new_event_tags' => ['nullable', 'string', 'max:500'],
            'process_images' => ['nullable', 'array', 'max:10'],
            'process_images.*' => ['image', 'max:5120'],
            'process_image_keys' => ['nullable', 'string'],
            'result_images' => ['nullable', 'array', 'max:10'],
            'result_images.*' => ['image', 'max:5120'],
            'result_image_keys' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '请输入事件名称。',
            'title.max' => '事件名称不能超过 150 个字符。',
            'status.required' => '请选择事件状态。',
            'status.in' => '事件状态无效。',
            'subject.max' => '来源/对象不能超过 255 个字符。',
            'occurred_on.date' => '发生日期格式不正确。',
            'visibility.required' => '请选择可见性。',
            'visibility.in' => '可见性无效。',
            'event_tag_ids.array' => '事件标签格式不正确。',
            'new_event_tags.max' => '新增事件标签内容过长。',
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
        ];
    }
}
