<?php include 'includes/header.php'; ?>

<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="glass-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="text-white fw-bold mb-0 italic text-xs">طلبات التوصيل النشطة</h6>
                <button class="btn btn-accent text-[9px] fw-bold px-3 py-1.5">ربط شركة شحن +</button>
            </div>

            <div class="row g-2">
                <?php 
                $deliveries = [
                    ['id' => 'DEL-01', 'cust' => 'أحمد علي', 'addr' => 'شارع 10، المقطم', 'status' => 'IN_TRANSIT', 'time' => '10 min'],
                    ['id' => 'DEL-02', 'cust' => 'سارة حسن', 'addr' => 'مدينتي، طلعت مصطفى', 'status' => 'PENDING', 'time' => '25 min'],
                    ['id' => 'DEL-03', 'cust' => 'محمود كمال', 'addr' => 'المعادي، دجلة', 'status' => 'DELIVERED', 'time' => 'تم'],
                ];
                foreach($deliveries as $d): ?>
                <div class="col-md-4">
                    <div class="p-3 bg-zinc-950/40 rounded-3 border border-zinc-900 h-100 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex gap-2 align-items-center">
                                <div class="bg-zinc-800 rounded-2 p-1.5">
                                    <i data-lucide="truck" class="text-zinc-600" style="width: 14px;"></i>
                                </div>
                                <div class="text-end">
                                    <p class="text-zinc-700 fw-bold uppercase mb-0" style="font-size: 7px;"><?php echo $d['id']; ?></p>
                                    <h6 class="text-zinc-300 fw-bold mb-0 text-[10px] text-truncate"><?php echo $d['cust']; ?></h6>
                                </div>
                            </div>
                            <span class="badge py-1 px-1.5 rounded-pill tracking-tight uppercase" style="font-size: 6px; background: rgba(245, 158, 11, 0.1); color: var(--accent-color); border: 1px solid rgba(245, 158, 11, 0.2);">
                                <?php echo $d['status']; ?>
                            </span>
                        </div>
                        
                        <div class="space-y-1 mb-3">
                            <div class="d-flex gap-2 align-items-center text-zinc-600" style="font-size: 9px;">
                                <i data-lucide="map-pin" style="width: 10px;"></i>
                                <span class="text-truncate"><?php echo $d['addr']; ?></span>
                            </div>
                            <div class="d-flex gap-2 align-items-center text-zinc-600" style="font-size: 9px;">
                                <i data-lucide="clock" style="width: 10px;"></i>
                                <span><?php echo $d['time']; ?></span>
                            </div>
                        </div>

                        <div class="row g-2 pt-2 border-top border-zinc-900 mt-2">
                            <div class="col-6">
                                <button class="btn btn-zinc-800 text-zinc-600 text-[8px] fw-bold w-full py-1 border-zinc-700">التفاصيل</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-warning text-[8px] fw-bold w-full py-1 border-amber-500/20 text-amber-500">تتبع</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="p-3 border-2 border-dashed border-zinc-950 rounded-3 text-center opacity-25">
    <i data-lucide="package" class="text-zinc-800 mb-2" style="width: 24px; height: 24px;"></i>
    <h6 class="text-zinc-600 fw-bold italic text-[10px]">جاهز للتكامل الآلي</h6>
</div>

<?php include 'includes/footer.php'; ?>
