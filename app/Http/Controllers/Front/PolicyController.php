<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\Session;

class PolicyController extends Controller
{
    public function privacy()
    {
        return $this->render('privacy');
    }

    public function cookie()
    {
        return $this->render('cookie');
    }

    public function legal()
    {
        return $this->render('legal');
    }

    public function quality()
    {
        $lang = $this->currentLocale();
        $content = config('company.' . $lang . '.quality', config('company.en.quality'));

        return view('frontend.company.quality', [
            'locale' => $lang,
            'content' => $content,
        ]);
    }

    public function about()
    {
        $lang = $this->currentLocale();
        $content = config('company.' . $lang . '.about', config('company.en.about'));

        return view('frontend.company.about', [
            'locale' => $lang,
            'content' => $content,
        ]);
    }

    protected function currentLocale(): string
    {
        $lang = Session::has('language')
            ? Language::find(Session::get('language'))
            : Language::where('is_default', 1)->first();

        return match (optional($lang)->language) {
            'Italian' => 'it',
            'Spanish' => 'es',
            'Russian' => 'ru',
            default => 'en',
        };
    }

    protected function render(string $type)
    {
        $locale = $this->currentLocale();

        $meta = [
            'privacy' => [
                'en' => [
                    'title' => 'Privacy Policy',
                    'subtitle' => 'Personal Data Protection Notice pursuant to Regulation (EU) 2016/679 (GDPR)',
                    'pdf' => 'privacy-policy-en.pdf',
                ],
                'it' => [
                    'title' => 'Informativa sulla Privacy',
                    'subtitle' => 'Informativa sul trattamento dei dati personali ai sensi del Regolamento (UE) 2016/679 (GDPR)',
                    'pdf' => 'privacy-policy-it.pdf',
                ],
                'es' => [
                    'title' => 'Política de Privacidad',
                    'subtitle' => 'Aviso de protección de datos personales conforme al Reglamento (UE) 2016/679 (GDPR)',
                    'pdf' => 'privacy-policy-en.pdf',
                ],
                'ru' => [
                    'title' => 'Политика конфиденциальности',
                    'subtitle' => 'Уведомление о защите персональных данных в соответствии с Регламентом (ЕС) 2016/679 (GDPR)',
                    'pdf' => 'privacy-policy-en.pdf',
                ],
            ],
            'cookie' => [
                'en' => [
                    'title' => 'Cookie Policy',
                    'subtitle' => 'Information regarding the use of cookies and tracking technologies',
                    'pdf' => 'cookie-policy-en.pdf',
                ],
                'it' => [
                    'title' => 'Cookie Policy',
                    'subtitle' => 'Informativa sull’utilizzo dei cookie e delle tecnologie di tracciamento',
                    'pdf' => 'cookie-policy-it.pdf',
                ],
                'es' => [
                    'title' => 'Política de Cookies',
                    'subtitle' => 'Información sobre el uso de cookies y tecnologías de seguimiento',
                    'pdf' => 'cookie-policy-en.pdf',
                ],
                'ru' => [
                    'title' => 'Политика cookie',
                    'subtitle' => 'Информация об использовании файлов cookie и технологий отслеживания',
                    'pdf' => 'cookie-policy-en.pdf',
                ],
            ],
            'legal' => [
                'en' => [
                    'title' => 'Legal Notice',
                    'subtitle' => 'Official legal information for the Industrialmac website',
                    'pdf' => null,
                ],
                'it' => [
                    'title' => 'Note Legali',
                    'subtitle' => 'Informazioni legali ufficiali del sito Industrialmac',
                    'pdf' => null,
                ],
                'es' => [
                    'title' => 'Aviso Legal',
                    'subtitle' => 'Información legal oficial del sitio web de Industrialmac',
                    'pdf' => null,
                ],
                'ru' => [
                    'title' => 'Правовая информация',
                    'subtitle' => 'Официальная правовая информация сайта Industrialmac',
                    'pdf' => null,
                ],
            ],
        ];

        $info = $meta[$type][$locale] ?? $meta[$type]['en'];
        $partialLocale = view()->exists("frontend.policies.partials.{$type}-{$locale}")
            ? $locale
            : 'en';

        return view('frontend.policies.show', [
            'type' => $type,
            'locale' => $locale,
            'title' => $info['title'],
            'subtitle' => $info['subtitle'],
            'pdfUrl' => !empty($info['pdf']) ? asset('assets/docs/' . $info['pdf']) : null,
            'partial' => "frontend.policies.partials.{$type}-{$partialLocale}",
        ]);
    }
}
