<?php include 'includes/header.php'; ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h6 class="text-white fw-bold mb-0 italic">طلبات التوصيل النشطة</h6>
                <button class="btn btn-accent text-[10px] fw-bold px-4">ربط شركة شحن +</button>
            </div>

            <div class="row g-4">
                <?php 
                $deliveries = [
                    ['id' => 'DEL-01', 'cust' => 'أحمد علي', 'addr' => 'شارع 10، المقطم', 'status' => 'IN_TRANSIT', 'time' => '10 min'],
                    ['id' => 'DEL-02', 'cust' => 'سارة حسن', 'addr' => 'مدينتي، طلعت مصطفى', 'status' => 'PENDING', 'time' => '25 min'],
                    ['id' => 'DEL-03', 'cust' => 'محمود كمال', 'addr' => 'المعادي، دجلة', 'status' => 'DELIVERED', 'time' => 'تم'],
                ];
                foreach($deliveries as $d): ?>
                <div class="col-md-4">
                    <div class="p-4 bg-zinc-950/40 rounded-4 border border-zinc-900 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="bg-zinc-800 rounded-3 p-2">
                                    <i data-lucide="truck" class="text-zinc-500" style="width: 18px;"></i>
                                </div>
                                <div class="text-end">
                                    <p class="text-zinc-700 fw-bold uppercase mb-0" style="font-size: 8px;"><?php echo $d['id']; ?></p>
                                    <h6 class="text-zinc-300 fw-bold mb-0 text-xs text-truncate"><?php echo $d['cust']; ?></h6>
                                </div>
                            </div>
                            <span class="badge py-1 px-2 rounded-2 tracking-tight uppercase" style="font-size: 7px; background: rgba(245, 158, 11, 0.1); color: var(--accent-color); border: 1px solid rgba(245, 158, 11, 0.2);">
                                <?php echo $d['status']; ?>
                            </span>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="d-flex gap-2 align-items-center text-zinc-500" style="font-size: 10px;">
                                <i data-lucide="map-pin" style="width: 12px;"></i>
                                <span><?php echo $d['addr']; ?></span>
                            </div>
                            <div class="d-flex gap-2 align-items-center text-zinc-500" style="font-size: 10px;">
                                <i data-lucide="clock" style="width: 12px;"></i>
                                <span><?php echo $d['time']; ?></span>
                            </div>
                        </div>

                        <div class="row g-2 pt-3 border-top border-zinc-900 mt-2">
                            <div class="col-6">
                                <button class="btn btn-zinc-800 text-zinc-500 text-[9px] fw-bold w-full py-1.5 border-zinc-700">التفاصيل</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-warning text-[9px] fw-bold w-full py-1.5 border-amber-500/20 text-amber-500">تتبع</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="p-5 border-2 border-dashed border-zinc-900 rounded-4 text-center opacity-25">
    <i data-lucide="package" class="text-zinc-700 mb-3" style="width: 48px; height: 48px;"></i>
    <h6 class="text-zinc-400 fw-bold italic">جاهز للتكامل الآلي</h6>
    <p class="text-zinc-600 mx-auto" style="font-size: 10px; max-width: 300px;">هذا القسم جاهز للربط مع API شركات الشحن والذكاء الاصطناعي لتخطيط المسارات.</p>
</div>

<?php include 'includes/footer.php'; ?>
