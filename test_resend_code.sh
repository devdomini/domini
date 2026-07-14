#!/bin/bash

echo "========================================"
echo "  Test Route resendVerificationCode"
echo "========================================"
echo ""

BASE_URL="http://localhost:8000/api"
PHONE="225748526787"

echo "Test: Renvoyer code de verification pour $PHONE"
echo ""
echo "Endpoint: $BASE_URL/auth/resend-verification-code"
echo ""
curl -X POST "$BASE_URL/auth/resend-verification-code" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"telephone\": \"$PHONE\"}"

echo ""
echo ""
echo "========================================"
echo "  Test termine"
echo "========================================"
echo ""
echo "Verifiez le SMS sur le numero $PHONE"
echo ""
