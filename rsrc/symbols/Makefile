SRCBMP := $(wildcard *.bmp)
JPGBMP := $(patsubst %.bmp,%.jpg,$(SRCBMP))

SRCPNG := $(wildcard *.png)
JPGPNG := $(patsubst %.png,%.jpg,$(SRCPNG))

SRCGIF := $(wildcard *.gif)
JPGGIF := $(patsubst %.gif,%.jpg,$(SRCGIF))

ALLJPG := $(wildcard *.jpg) $(JPGBMP) $(JPGGIF) $(JPGPNG)

THUMBS := $(patsubst %.jpg,thumbs/%.jpg,$(ALLJPG))

ICONS := $(patsubst %.jpg,icons/%.ico,$(ALLJPG))

EPS := $(patsubst %.jpg,tex-pics/%.eps,$(ALLJPG))

DIRS := $(wildcard ???)

Total:	AllHere AllSub Makefile

AllSub:	$(DIRS)
	for i in $(DIRS); do make -C $$i; done

AllHere:	$(JPGBMP) $(JPGPNG) $(JPGGIF) $(THUMBS)

%.jpg:	%.bmp
	bmptopnm < $< |pnmtojpeg > $@
	
%.jpg:	%.png
	pngtopnm < $< |pnmtojpeg > $@

%.jpg:	%.gif
	giftopnm < $< |pnmtojpeg > $@

thumbs/%.jpg:	%.jpg Makefile
	jpegtopnm < $< |pamscale -xyfit 64 64 |pnmtojpeg > $@

tex-pics/%.eps:	%.jpg
	jpegtopnm < $< |pamscale -xyfit 200 200 |pnmtojpeg > /tmp/_conv_jpg_eps.jpg
	convert /tmp/_conv_jpg_eps.jpg $@

_112:
	$(MAKE) -C 112

_170:
	$(MAKE) -C 170

_210:
	$(MAKE) -C 210

_220:
	$(MAKE) -C 220

_244:
	$(MAKE) -C 244

_511:
	$(MAKE) -C 511

_3BS:
	$(MAKE) -C 3BS

_COX:
	$(MAKE) -C COX

_BMS:
	$(MAKE) -C BMS

_BTC:
	$(MAKE) -C BTC

_GRT:
	$(MAKE) -C GRT

_HGM:
	$(MAKE) -C HGM

_KMN:
	$(MAKE) -C KMN

_MAU:
	$(MAKE) -C MAU

_NTL:
	$(MAKE) -C NTL

_ORO:
	$(MAKE) -C ORO

_PKT:
	$(MAKE) -C PKT

_PSC:
	$(MAKE) -C PSC

_RMK:
	$(MAKE) -C RMK

_SCH:
	$(MAKE) -C SCH

_SMS:
	$(MAKE) -C SMS
