import peewee
from peewee import *

db = MySQLDatabase('aatw', user='root',passwd='SQLPw0rd')

class Book(peewee.Model):
    author = peewee.CharField()
    title = peewee.TextField()

    class Meta:
        database = db

Book.create_table()
book = Book(author="me", title='Peewee is cool')
book.save()
for book in Book.filter(author="me"):
    print book.title

Peewee is cool
