data = [
    "áž€áž¶ážšáž”áž‰áŸ’áž…áž¼áž›áž¯áž€ážŸáž¶ážš",
    "ážŸáž»áž  ážŠáž¶ážšáž¶",
    "ážŸáž¶ ážœáŸ‰áž¶áž˜áž·áž‰",
    "áž¢áŸ’áž“áž€áž”áž¶áž“áž…áž»áŸ‡ážˆáŸ’áž˜áŸ„áŸ‡ážŠáŸ„áž™áž‡áŸ„áž‚áž‡áŸ áž™ ážŸáŸ†ážŽáž¶áž„áž›áŸ’áž¢ áž‡áž½áž”áž‚áŸ’áž…áž¶áž†áž¶áž”áŸ‹áŸ—áŸ”",
    "áž¢áŸ’áž“áž€ážŸáž¶áž€áž›áŸ’áž”áž„",
    "áž”áž»áž‚áŸ’áž‚áž›áž·áž€ážŸáž¶áž€áž›áŸ’áž”áž„",
    "áž™áž»áž‘áŸ’áž’áŸ„ ážŸáž¶ážœáž˜áž·áž‰"
]

def decode_mojibake(s):
    try:
        # Encode with latin-1 to get bytes, then decode as utf-8
        # However, some characters like € (0x80) are not in latin-1.
        # We use Windows-1252 because that's where € and ž come from.
        b = s.encode('cp1252')
        return b.decode('utf-8')
    except Exception as e:
        return f"Error: {e}"

for s in data:
    print(f"'{s}' -> '{decode_mojibake(s)}'")
