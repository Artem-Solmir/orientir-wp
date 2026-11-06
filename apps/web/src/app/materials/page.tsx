import { MaterialsForm } from "./form";

export const metadata = {
  title: "Прислать материалы",
  description: "Отправьте отчёт и фотографии похода в клуб «Ориентир».",
  alternates: { canonical: "/materials" },
};

export default function MaterialsPage() {
  return (
    <div className="container">
      <h1 className="page-title">Прислать материалы</h1>
      <div className="prose">
        <p>Были в походе или на мероприятии клуба? Расскажите о нём, мы опубликуем отчёт на сайте.</p>
        <p>Фотографии на сайт не загружаются. Выложите их в Яндекс Диск и пришлите ссылку:</p>
        <ol>
          <li>Создайте на Яндекс Диске папку и загрузите в неё фотографии.</li>
          <li>Нажмите «Поделиться» и включите доступ по ссылке.</li>
          <li>Скопируйте ссылку (disk.yandex.ru/d/...) и вставьте в форму ниже.</li>
        </ol>
        <p>Папку не удаляйте и не закрывайте, пока отчёт не опубликован.</p>
      </div>
      <MaterialsForm />
    </div>
  );
}
