# Тестовое задание на вакансию PHP разработчик (Laravel 10, PHP 8.1)

Ссылка на вакансию https://hh.ru/vacancy/85449206

С помощью PHP обработать входящий JSON ответ и найти свободные интервалы времени для записи на сеанс.

`seances` - сеансы, на которые уже есть записи (занятое время)
`seance_length` - продолжительность сеанса в секундах
`seance_date`: дата сеанса (в формате iso8601)

Время работы сотрудника с 10:00 до 20:00. Во входящем запросе пришли данные о его занятом времени.
Клиент хочет записаться на услугу длительностью 45 минут.

Необходимо вывести массив всех доступных интервалов (слотов) для записи клиента с учетом уже занятого времени в расписании сотрудника.

Пример: ```[“11:10”, “14:35”]```

Нужно прислать ссылку на репозиторий проекта Laravel до 24.08.2023 23:59 мне в Телеграм.

Входящий JSON ответ:
```
{
  "success": true,
  "data": {
    "seance_date": 1492041600,
    "seances": [
      {
        "time": "10:00",
        "seance_length": 3600
      },
      {
        "time": "12:15",
        "seance_length": 1800
      },
      {
        "time": "15:30",
        "seance_length": 3600
      },
      {
        "time": "19:45",
        "seance_length": 7200
      }
    ]
  },
  "meta": []
}

```