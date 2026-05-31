<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Enums\TransactionType;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'خطاب رسمي',
                'type' => TransactionType::OUTGOING,
                'subject' => 'خطاب رسمي بخصوص [الموضوع]',
                'content' => "\nتحية طيبة وبعد،\n\nبالإشارة إلى الموضوع أعلاه، نفيدكم بأن...\n\nوتفضلوا بقبول فائق الاحترام.\n",
            ],
            [
                'name' => 'تعميم داخلي',
                'type' => TransactionType::CIRCULAR,
                'subject' => 'تعميم بخصوص [الموضوع]',
                'content' => "\nإلى جميع الأقسام المعنية،\n\nالسلام عليكم ورحمة الله وبركاته،\n\nنود إفادتكم بما يلي...\n\nوالله ولي التوفيق.\n",
            ],
            [
                'name' => 'قرار إداري',
                'type' => TransactionType::DECISION,
                'subject' => 'قرار إداري رقم [الرقم]',
                'content' => "\nقرار إداري\n\nبعد الاطلاع على...\n\nقررنا ما يلي:\n\nالمادة الأولى: [نص القرار]\nالمادة الثانية: [نص القرار]\nالمادة الثالثة: يعمل بهذا القرار اعتباراً من تاريخه.\n",
            ],
            [
                'name' => 'مذكرة داخلية',
                'type' => TransactionType::INTERNAL,
                'subject' => 'مذكرة بخصوص [الموضوع]',
                'content' => "\nإلى: [القسم/الشخص المعني]\nمن: [اسم المرسل]\nالموضوع: [الموضوع]\nالتاريخ: [التاريخ]\n\nنود إحاطتكم علماً بما يلي...\n",
            ],
            [
                'name' => 'محضر اجتماع',
                'type' => TransactionType::INTERNAL,
                'subject' => 'محضر اجتماع [الموضوع]',
                'content' => "\nمحضر اجتماع\n\nالتاريخ: [التاريخ]\nالمكان: [المكان]\nالحضور:\n1. [الاسم]\n2. [الاسم]\n\nموضوع الاجتماع: [الموضوع]\n\nالنقاط المناقشة:\n...\n\nالتوصيات:\n1. [التوصية]\n2. [التوصية]\n",
            ],
        ];

        foreach ($templates as $template) {
            Template::create([
                ...$template,
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}
